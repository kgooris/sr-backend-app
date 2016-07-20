<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * OrderRepository
 *
 */
class OrderRepository extends EntityRepository
{
	
	public function count()
	{
		$qb = $this->getEntityManager()->getRepository("AppBundle:Order")->createQueryBuilder('t');
		return $qb
			->select('count(t.id)')
			->getQuery()
			->getSingleScalarResult();
	}
}
