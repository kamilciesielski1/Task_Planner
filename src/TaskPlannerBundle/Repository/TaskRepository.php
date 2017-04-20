<?php

namespace TaskPlannerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TaskRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaskRepository extends EntityRepository
{
    public function findAllByStatus($id)
    {
        $data = $this->getEntityManager()->createQuery('Select tasks From TaskPlannerBundle:Task tasks Where tasks.user = '.$id.' And tasks.status = 1 Order By tasks.deadLine ASC')
                ->getResult();
        return $data;
    }
    public function findAllByUserIdOrderTask($id)
    {
        $data = $this->getEntityManager()->createQuery('Select tasks From TaskPlannerBundle:Task tasks Where tasks.user = '.$id.' Order By tasks.id DESC')
                ->getResult();
        return $data;
    }
    public function findAllbyDate($id)
    {
        $data = $this->getEntityManager()->createQuery('Select tasks From TaskPlannerBundle:Task tasks Where tasks.user = '.$id.' And tasks.deadLine >= :today And tasks.status = 0 Order By tasks.deadLine ASC')->setParameter('today', new \DateTime())
                ->getResult();
        return $data;
    }
    public function findAllbyOutOfDate($id)
    {
        $data = $this->getEntityManager()->createQuery('Select tasks From TaskPlannerBundle:Task tasks Where tasks.user = '.$id.' And tasks.deadLine < :today And tasks.status = 0 Order By tasks.deadLine ASC')->setParameter('today', new \DateTime())
                ->getResult();
        return $data;
    }
   
}
