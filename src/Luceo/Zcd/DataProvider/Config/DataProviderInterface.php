<?php
namespace Luceo\Zcd\DataProvider\Config;

use AppBundle\Entity\CustomerSystem;

interface DataProviderInterface
{
    /**
     * Returns all the configuration values for a given customer system and config file
     * @param CustomerSystem $customerSystem
     * @param $file
     * @return array Configuration key => Configuration value
     * @throws \RuntimeException
     */
    public function getConfiguration(CustomerSystem $customerSystem, $file);
}