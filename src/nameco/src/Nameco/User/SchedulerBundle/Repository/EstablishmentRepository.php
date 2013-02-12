<?php

namespace Nameco\User\SchedulerBundle\Repository;

use Nameco\User\SchedulerBundle\Entity\Schedule;

use Doctrine\ORM\EntityRepository;

class EstablishmentRepository extends EntityRepository
{
    public function isNotBooking(Schedule $schedule)
    {
        $establishment = $schedule->getEstablishment();
        if ($establishment == null || count($establishment) === 0)
        {
            return true;
        }
        $query = $this->getEntityManager()->createQuery(
                'SELECT COUNT(e) FROM NamecoUserSchedulerBundle:Establishment e
                JOIN e.schedule s
                WHERE e.id = :id
                AND (
                (s.startDatetime >= :startDateTime AND s.endDatetime <= :endDateTime)
                    OR (s.startDatetime >= :startDateTime AND s.startDatetime < :endDateTime)
                    OR (s.endDatetime > :startDateTime AND s.endDatetime <= :endDateTime))');
        $startDateTime = $schedule->getStartDatetime();
        $endDateTime = $schedule->getEndDatetime();
        foreach ($establishment as $e)
        {
            $query->setParameter('id', $e->getId())
                    ->setParameter('startDateTime', $startDateTime)
                    ->setParameter('endDateTime', $endDateTime);
            if ($query->getSingleScalarResult() == 0)
            {
                return false;
            }
            $query->free();
        }
        return true;
    }
}
