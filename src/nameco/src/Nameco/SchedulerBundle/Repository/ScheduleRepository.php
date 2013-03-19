<?php

namespace Nameco\SchedulerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{
    public function getUserMonthSchedules($userId, $firstDay, $lastDay)
    {
        $query = $this->createQueryBuilder('s')
				->leftJoin('s.user', 'u')
				->where('u.id = :userId')
				->andWhere('(s.startDatetime >= :firstDay AND s.startDatetime < :lastDay)'
						. ' OR (s.endDatetime >= :firstDay AND s.endDatetime < :lastDay)'
						. ' OR (:firstDay >= s.startDatetime AND :lastDay < s.endDatetime)'
						)
				->orderBy('s.startDatetime', 'ASC')
				->setParameter('userId', $userId)
				->setParameter('firstDay', $firstDay)
				->setParameter('lastDay', $lastDay)
				->getQuery();

        return $query->getResult();
    }
}
