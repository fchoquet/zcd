<?php
namespace Luceo\Zcd\DataProvider\Config;

use AppBundle\Entity\CustomerSystem;
use Profilsoft\Legacy\XmlPathReader;
use Symfony\Component\Process\Process;

/**
 * This class retrieves configuration via scp commands
 * This is temporary until the API is able the configuration values
 */
class ScpDataProvider implements DataProviderInterface
{
    /**
     * @var XmlPathReader
     */
    private $converter;

    public function __construct()
    {
        $this->converter = new XmlPathReader();
    }


    /**
     * Returns all the configuration values for a given customer system and config file
     * @param CustomerSystem $customerSystem
     * @param $file
     * @return array Configuration key => Configuration value
     * @throws \RuntimeException
     */
    public function getConfiguration(CustomerSystem $customerSystem, $file)
    {
        $cmd = sprintf(
            'ssh dev-http \'cat /www/%s/web/param/xml/%s\' 2>&1',
            $customerSystem->getName(),
            $file
        );

        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $xml = $process->getOutput();

        // Cleanup the unwanter "Killed by signal 1." string at the end
        $xml = trim(str_replace('Killed by signal 1.', '', $xml));

        return $this->converter->getArray($xml);
    }
}