<?php

namespace Nameco\SchedulerBundle\Repository;

use Nameco\SchedulerBundle\Entity\Schedule;

use Doctrine\ORM\EntityRepository;

class EstablishmentRepository extends EntityRepository
{
    public function getOne()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
                        SELECT e
                        FROM NamecoSchedulerBundle:Establishment e
                        ORDER BY e.id')
                        ->setMaxResults(1);
        $ids = $query->getResult();
        return $ids[0];
    }

    /**
     * @param type $id
     * @param type $firstDay
     * @param type $searchLastDay
     * @return type
     */
    public function getMonthSchedules($id, $firstDay, $searchLastDay)
    {
        $em = $this->getEntityManager();
    	$query = $em->createQuery('
    			SELECT s FROM NamecoSchedulerBundle:Schedule s
    			JOIN s.establishment e
    			WHERE e.id = :id
    			AND (
                                (s.startDatetime >= :firstDay AND s.startDatetime < :lastDay)
                            OR  (s.startDatetime < :firstDay AND s.endDatetime > :lastDay)
                        )
    			ORDER BY s.startDatetime ASC')
    	->setParameter('id',       $id)
    	->setParameter('firstDay', $firstDay)
    	->setParameter('lastDay',  $searchLastDay);
    	return $query->getResult();
    }

    public function isBooking(Schedule $schedule)
    {
        $establishment = $schedule->getEstablishment();
        if ($establishment == null || count($establishment) === 0)
        {
            return false;
        }
        $em = $this->getEntityManager();
        $startDateTime = $schedule->getStartDatetime();
        $endDateTime = $schedule->getEndDatetime();
        foreach ($establishment as $e)
        {
            $query = $em->createQuery(
                    'SELECT COUNT(e) FROM NamecoSchedulerBundle:Establishment e
                    JOIN e.schedule s
                    WHERE e.id = :id
                    AND (
                    (s.startDatetime >= :startDateTime AND s.endDatetime <= :endDateTime)
                    OR (s.startDatetime < :startDateTime AND s.endDatetime > :endDateTime)
                    OR (s.startDatetime >= :startDateTime AND s.startDatetime < :endDateTime)
                    OR (s.endDatetime > :startDateTime AND s.endDatetime <= :endDateTime))');
            $query->setParameter('id', $e->getId())
                    ->setParameter('startDateTime', $startDateTime)
                    ->setParameter('endDateTime', $endDateTime);
            if ($query->getSingleScalarResult() != 0)
            {
                return true;
            }
        }
        return false;
    }
}