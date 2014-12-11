<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ConfigKeyRepository extends EntityRepository
{
    public function getValueCountByKey($file)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT k as key, COUNT(v.id) as nb_values FROM AppBundle:ConfigKey k
                JOIN k.values v
                WHERE k.file = :file
                GROUP BY k'
            )->setParameter('file', $file);

        return $query->getResult();
    }
}
