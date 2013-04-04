<?php

namespace Nameco\SchedulerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Nameco\SchedulerBundle\Entity\Schedule;

class EstablishmentRepository extends EntityRepository
{
	/**
	 * 施設の先頭を取得する
	 * 
	 * @return null
	 */
    public function getOne()
    {
		$q = $this->createQueryBuilder('e')
				->select('e, a')
				->where('e.enabled=true')
				->leftJoin('e.area', 'a')
				->orderBy('e.id', 'ASC')
				->setMaxResults(1)
				->getQuery();
		try
		{
			$estab = $q->getSingleResult();
        } catch (NoResultException $e) {
			return null;
        }
        return $estab;
	}

    /**
     * @param type $id
     * @param type $firstDay
     * @param type $searchLastDay
     * @return type
     */
    public function getMonthSchedules($id, $firstDay, $searchLastDay)
    {
        return $this->getEntityManager()->createQuery(
				'SELECT s FROM NamecoSchedulerBundle:Schedule s'
				. ' JOIN s.establishment e WHERE e.id = :id AND ((s.startDatetime >= :firstDay AND s.startDatetime < :lastDay)'
				. ' OR  (s.startDatetime < :firstDay AND s.endDatetime > :lastDay)) ORDER BY s.startDatetime ASC')
    	->setParameter('id',       $id)
    	->setParameter('firstDay', $firstDay)
    	->setParameter('lastDay',  $searchLastDay)
		->getResult();
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
	
	/**
	 * エリアが設定されている施設の数を返す
	 * 
	 * @param type $id エリアID
	 * @return type 施設数
	 */
	public function countArea($id)
	{
		return $this->createQueryBuilder('e')
				->select('COUNT(e.id)')
				->leftJoin('e.area', 'a')
				->where('a.id=:area_id')
				->setParameter('area_id', $id)
				->getQuery()->getSingleScalarResult();
	}
}
