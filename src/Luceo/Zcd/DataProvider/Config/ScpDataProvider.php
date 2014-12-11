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
            if ($process->getExitCode() == 1) {
                // Error 1 is probably not a ssh error, so it's the cat command
                // We ignore
                return array();
            }
            throw new \RuntimeException($process->getErrorOutput());
        }

        $xml = $process->getOutput();

        // Cleanup the unwanted "Killed by signal 1." string at the end
        $xml = trim(str_replace('Killed by signal 1.', '', $xml));

        try {
            return $this->converter->getArray($xml);
        } catch (\RuntimeException $e) {
            // Not a valid xml, so the client system is probably not active
            return array();
        }
    }
}