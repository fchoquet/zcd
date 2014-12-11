<?php
namespace AppBundle\Command;

use AppBundle\Entity\CustomerSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Luceo\Zcd\DataProvider\CustomerSystem\DataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCustomerSystemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sync:customer:systems')
            ->setDescription('Synchronizes the local customer systems list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        /** @var CustomerSystemRepository $repository */
        $repository = $em->getRepository('AppBundle:CustomerSystem');

        if ($repository->hasData()) {
            throw new \RuntimeException('The table is not empty');
        }

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $this->getContainer()->get('customer_system_data_provider');

        $count = 0;
        foreach ($dataProvider->getAllSystems() as $customerSystem) {
            $em->persist($customerSystem);
            $count ++;
        }
        $em->flush();

        $output->writeln(sprintf('%s customer systems imported', $count));
    }
}
