<?php

namespace Nameco\SchedulerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AreaRepository extends EntityRepository
{
	
	public function findEstablishmentArea()
	{
		return $this->createQueryBuilder('a')
			->select('a, e')
			->where('e.enabled=true')
			->leftJoin('a.establishments', 'e')
			->orderBy('a.name', 'ASC')
			->getQuery()
			->getResult();
	}
	
	
}
