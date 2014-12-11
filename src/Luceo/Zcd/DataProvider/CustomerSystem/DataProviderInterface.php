<?php
namespace Luceo\Zcd\DataProvider\CustomerSystem;

use AppBundle\Entity\CustomerSystem;

interface DataProviderInterface
{
    /**
     * @return CustomerSystem[]
     * @throws \RuntimeException
     */
    public function getAllSystems();
}