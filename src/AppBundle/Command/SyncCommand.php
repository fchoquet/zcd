<?php
namespace AppBundle\Command;

use AppBundle\Entity\ConfigKeyRepository;
use AppBundle\Entity\ConfigValueRepository;
use AppBundle\Entity\CustomerSystem;
use AppBundle\Entity\CustomerSystemRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Luceo\Zcd\DataProvider\Config\DataProviderInterface as ConfigDataProviderInterface;
use Luceo\Zcd\DataProvider\CustomerSystem\DataProviderInterface as CustomerSystemDataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Connection */
    private $connection;

    private $xmlFiles = array(
        'agenda.xml',
        'bannette.xml',
        'bureau.xml',
        'candidat.xml',
        'competence.xml',
        'correspondance.xml',
        'courrier.xml',
        'crm.xml',
        'embauche.xml',
        'entretien.xml',
        'forms.xml',
        'front_office.xml',
        'import.xml',
        'mailing.xml',
        'menu.xml',
        'ofccp.xml',
        'poste.xml',
        'publication.xml',
        'recherche.xml',
        'referentiels.xml',
        'relance.xml',
        'req-param.xml',
        'rest.xml',
        'site_emploi.xml',
        'sq.xml',
        'statistique.xml',
        'utilisateur.xml',
    );

    protected function configure()
    {
        $this
            ->setName('zcd:sync')
            ->setDescription('Synchronizes the local database')
            ->addOption('force', null, InputOption::VALUE_NONE, 'If set, allows to overwrite existing data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->connection = $this->getContainer()->get('database_connection');
        $this->connection->getConfiguration()->setSQLLogger(null);

        /** @var CustomerSystemRepository $repository */
        $repository = $this->em->getRepository('AppBundle:CustomerSystem');

        if ($repository->hasData() && ! $input->getOption('force')) {
            throw new \RuntimeException('The database is not empty. Use --force option');
        }

        $this->connection->exec('DELETE FROM customer_system_config_value');
        $this->connection->exec('DELETE FROM config_value');
        $this->connection->exec('DELETE FROM config_key');
        $this->connection->exec('DELETE FROM customer_system');

        // Note that we can't built a giant tree in memory
        // So we import customer systems, flush, then we can't work with the ORM anymore
        $count = $this->importCustomerSystems();
        $output->writeln(sprintf('%s customer systems imported', $count));

        foreach ($repository->findAll() as $customerSystem) {
            $output->writeln(sprintf('# %s:', $customerSystem->getName()));
            $this->importConfig($customerSystem,  $output);
        }
        $output->writeln('configuration data imported');
    }

    private function importCustomerSystems()
    {
        /** @var CustomerSystemDataProviderInterface $dataProvider */
        $dataProvider = $this->getContainer()->get('customer_system_data_provider');

        $count = 0;
        foreach ($dataProvider->getAllSystems() as $customerSystem) {
            $this->em->persist($customerSystem);
            $count ++;
        }
        $this->em->flush();

        return $count;
    }

    private function importConfig(CustomerSystem $customerSystem, OutputInterface $output)
    {
        /** @var ConfigDataProviderInterface $dataProvider */
        $dataProvider = $this->getContainer()->get('config_data_provider');

        foreach ($this->xmlFiles as $xmlFile) {
            $output->write(sprintf('- importing %s ...', $xmlFile));

            foreach ($dataProvider->getConfiguration($customerSystem, $xmlFile) as $xPath => $value) {
                $keyId = $this->makeSureKeyExist($xmlFile, $xPath);
                $valueId = $this->makeSureValueExist($keyId, $value);

                $this->connection->insert(
                    'customer_system_config_value',
                    array(
                        'config_value_id' => $valueId,
                        'customer_system_id' => $customerSystem->getId(),
                    )
                );

                unset($valueId);
            }

            $output->writeln('<info>done</info>');
        }
    }

    /**
     * @param $file
     * @param $path
     * @return null|string
     * @throws \Doctrine\DBAL\DBALException
     */
    private function makeSureKeyExist($file, $path)
    {
        $statement = $this->connection->prepare(
            'SELECT id FROM config_key WHERE file = :file AND path = :path'
        );

        $statement->bindValue('file', $file);
        $statement->bindValue('path', $path);
        $rows = $statement->fetchColumn(0);
        $id = isset($rows[0]) ? $rows[0] : null;
        $statement->closeCursor();

        if ($id) {
            return $id;
        }

        $this->connection->insert(
            'config_key',
            array(
                'file' => $file,
                'path' => $path,
            )
        );

        return $this->connection->lastInsertId();
    }

    private function makeSureValueExist($keyId, $value)
    {
        $statement = $this->connection->prepare(
            'SELECT id FROM config_value WHERE config_key_id = :config_key_id AND value_hash = :value_hash'
        );

        $statement->bindValue('config_key_id', $keyId);
        $statement->bindValue('value_hash', sha1($value));
        $rows = $statement->fetchColumn(0);
        $id = isset($rows[0]) ? $rows[0] : null;
        $statement->closeCursor();

        if ($id) {
            return $id;
        }

        $this->connection->insert(
            'config_value',
            array(
                'config_key_id' => $keyId,
                'value' => $value,
                'value_hash' => sha1($value)
            )
        );

        return $this->connection->lastInsertId();
    }
}
