<?php

namespace Nameco\User\EstablishmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EstablishmentController extends Controller
{
    /**
     * @Route("/establishment/month/{id}/{year}/{month}")
     * @Route("/establishment/month/", name="establishment_month")
     */
    public function monthAction($id = null, $year = null, $month = null)
    {
    	if ($id == null )
    	{
    		$year  = date("Y");
    		$month = date("m");
    		$em = $this->getDoctrine()->getEntityManager();
    		$query = $em->createQuery('
    				SELECT e
    				FROM NamecoUserEstablishmentBundle:Establishment e
    				ORDER BY e.id')
    				->setMaxResults(1);
    		$ids = $query->getResult();
    		$id  = $ids[0]->getId();
    	}
    	
		// 月の初日が日曜でなければ日曜までずらす
    	$firstDay = new \DateTime($year.'-'.$month.'-1');
    	$firstDay->modify('-' .($firstDay->format('w') -1) .' day');

    	// 月の最終日が土曜でなければ土曜までずらす
    	$lastDay = new \DateTime('last day of '.$year.'-'.$month);
    	$diffWeek = 6 - $lastDay->format('w') + 1;
    	$lastDay->modify('+'.$diffWeek.' day');
    	// DBからの検索用最終日を作成(カレンダーの最終日+1日)
    	$searchLastDay = clone $lastDay;
    	$searchLastDay->modify("+1 day");


    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT s FROM NamecoUserEstablishmentBundle:Schedule s
    			JOIN s.establishment e
    			WHERE e.id = :id
    			AND (
    			(s.startDatetime >= :firstDay AND s.startDatetime < :lastDay)
    			OR (s.startDatetime < :firstDay AND s.endDatetime > :lastDay)
    	)
    			ORDER BY s.startDatetime ASC')
    	->setParameter('id',       $id)
    	->setParameter('firstDay', $firstDay)
    	->setParameter('lastDay',  $searchLastDay);

    	$result = $query->getResult();
    	
    	$diff = $firstDay->diff($lastDay);
    	$week = (intval($diff->format( '%a' )) + 1) / 7;

    	return $this->render('NamecoUserEstablishmentBundle:Establishment:month.html.twig', 
    			array(
    					'start'     => $firstDay, 
    					'end'       => $lastDay,
    					'week'      => $week, 
    					'schedules' => $result));
    }
}
