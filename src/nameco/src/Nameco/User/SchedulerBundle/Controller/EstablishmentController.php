<?php

namespace Nameco\User\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EstablishmentController extends Controller
{
    /**
     * @Route("/establishment/month/{id}/{year}/{month}", name="establishment_month_id")
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
    				FROM NamecoUserSchedulerBundle:Establishment e
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
    			SELECT s FROM NamecoUserSchedulerBundle:Schedule s
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

    	// ナビゲーション用データ
    	$dispDate = new \DateTime($year.'-'.$month.'-1');
    	
    	// 施設名
    	$e      = $em->find('NamecoUserSchedulerBundle:Establishment', $id);
    	$area   = $e->getArea();
    	$e_name = $area[0]->getName() . ' ' . $e->getName(); 

    	return $this->render('NamecoUserSchedulerBundle:Establishment:month.html.twig',
    			array(
    					'start'           => $firstDay,
    					'end'             => $lastDay,
    					'week'            => $week,
    					'schedules'       => $result,
    					'id'              => $id,
    					'userId'          => $this->getUser()->getId(),
    					'dispDate'        => $dispDate,
    					'dispTargetLabel' => $e_name,
						'year'            => $year,
						'month'           => $month));
    }

    /**
     * @Route("/establishmentSelectDate", name="establishment_select_date")
     * @Template()
     */
    public function establishmentSelectDateAction()
    {
    	$request = $this->getRequest();
    	$linkParam['id'] = $request->request->get('idVal');
    	$linkParam['year'] = $request->request->get('y');
    	$linkParam['month'] = $request->request->get('m');
    	// 表示対象にリダイレクト
    	return $this->redirect($this->generateUrl('establishment_month_id', $linkParam));
    }
}
