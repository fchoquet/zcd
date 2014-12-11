<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CustomerSystemRepository extends EntityRepository
{
    public function hasData()
    {
        $count = $this->getEntityManager()
            ->createQuery(
                'SELECT count(c) FROM AppBundle:CustomerSystem c'
            )
            ->getSingleScalarResult();

        return $count > 0;
    }
}
