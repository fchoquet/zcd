<?php
namespace Luceo\Zcd\DataProvider\Config;

use AppBundle\Entity\CustomerSystem;
use Profilsoft\XmlDiff\XmlToFlatArrayConverter;
use Symfony\Component\Process\Process;

/**
 * This class retrieves configuration via local file system
 */
class LocalProvider implements DataProviderInterface
{
    /**
     * @var XmlToFlatArrayConverter
     */
    private $converter;

    public function __construct()
    {
        $this->converter = new XmlToFlatArrayConverter();
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
        $path = sprintf(
            '/www/%s/web/param/xml/%s',
            $customerSystem->getName(),
            $file
        );

        if (! file_exists($path)) {
            return array();
        }

        $xml = file_get_contents($path);

        return $this->converter->convert($xml);
    }
}