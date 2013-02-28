<?php

namespace Nameco\SchedulerBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{
    public function getUserMonthSchedules($userId, $firstDay, $lastDay)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT s FROM NamecoSchedulerBundle:Schedule s
            LEFT JOIN s.user u
            WHERE u.id = :userId
            AND (
            (s.startDatetime >= :firstDay AND s.startDatetime < :lastDay) OR (s.startDatetime < :firstDay AND s.endDatetime > :lastDay))
            ORDER BY s.startDatetime ASC'
        )
        ->setParameter('userId', $userId)
        ->setParameter('firstDay', $firstDay)
        ->setParameter('lastDay', $lastDay);

        return $query->getResult();
    }
}
