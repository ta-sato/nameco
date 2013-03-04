<?php

namespace Nameco\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EstablishmentController extends SchedulerBaseController
{
    /**
     * 施設予約月
     * @Route("/establishment/month/{id}/{year}/{month}", name="establishment_month_id")
     * @Route("/establishment/month/", name="establishment_month")
     */
    public function monthAction($id = null, $year = null, $month = null)
    {
        $repository = $this->getDoctrine()->getEntityManager()->getRepository('NamecoSchedulerBundle:Establishment');

		// 施設が選択されていない場合は施設の先頭を選択する
    	if ($id == null )
    	{
            $year  = date("Y");
            $month = date("m");
            $estab  = $repository->getOne();
    	}
		else
		{
	    	$estab  = $repository->find($id);
		}

		if ($estab == null)
		{
			// TODO
			return $this->render('NamecoSchedulerBundle:Establishment:empty.html.twig', array(
			));
		}
		
        list($firstDay, $lastDay, $week, $dispDate) = $this->calcMonthRange($year, $month);
        $result = $repository->getMonthSchedules($id, $firstDay, $lastDay);

    	// 施設名
//    	$area   = $estab->getArea();
//    	$e_name = $area->getName() . ' ' . $estab->getName();

    	return $this->render('NamecoSchedulerBundle:Establishment:month.html.twig',
				array(
					'start'           => $firstDay,
					'end'             => $lastDay,
					'week'            => $week,
					'schedules'       => $result,
//					'id'              => $id,
//					'userId'          => $this->getUser()->getId(),
					'dispDate'        => $dispDate,
//					'dispTargetLabel'	=> $e_name,
					'year'            => $year,
					'month'           => $month,
					'estab'				=> $estab,
					));
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
