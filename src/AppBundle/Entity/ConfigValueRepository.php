<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ConfigValueRepository extends EntityRepository
{
    public function getCustomerSystemCountByValue($configKeyId)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT v as value, COUNT(cs.id) as nb_cs FROM AppBundle:ConfigValue v
                JOIN v.customerSystems cs
                WHERE v.configKey = :config_key_id
                GROUP BY v'
            )->setParameter('config_key_id', $configKeyId);

        return $query->getResult();
    }
}
