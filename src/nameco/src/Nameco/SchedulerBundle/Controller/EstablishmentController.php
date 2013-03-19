<?php

namespace Nameco\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Nameco\SchedulerBundle\Form\EstablishmentType;
use Nameco\SchedulerBundle\Entity\Establishment;

class EstablishmentController extends SchedulerBaseController
{
    /**
     * 施設予約月
     * @Route("/establishment/month/{id}/{year}/{month}", name="establishment_month_id")
     * @Route("/establishment/month/", name="establishment_month")
     */
    public function monthAction($id = null, $year = null, $month = null)
    {
		$em = $this->getDoctrine()->getManager();
		
        $repository = $em->getRepository('NamecoSchedulerBundle:Establishment');

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
        $result = $repository->getMonthSchedules($estab->getId(), $firstDay, $lastDay);

    	// 施設名
//    	$area   = $estab->getArea();
//    	$e_name = $area->getName() . ' ' . $estab->getName();
        $areas = $em->getRepository('NamecoSchedulerBundle:Area')
				->createQueryBuilder('es')
				->orderBy('es.name', 'ASC')
				->getQuery()
				->getResult();

    	return $this->render('NamecoSchedulerBundle:Establishment:month.html.twig',
				array(
					'start'           => $firstDay,
					'end'             => $lastDay,
					'week'            => $week,
					'schedules'       => $result,
					'dispDate'        => $dispDate,
					'year'            => $year,
					'month'           => $month,
					'estab'			=> $estab,
					'areas' => $areas,
					'user'  => $user = $this->getUser(),
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

	/**
     * @Route("/establishment/new", name="establishment_new")
	 */
	public function newAction(Request $request)
	{
        $entity  = new Establishment();
        $form    = $this->createForm(new EstablishmentType(), $entity);
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);
                $em->flush();
                
                return $this->redirect($this->generateUrl('establishment_month'));
            }
        }

        $renderParam = array(
            'entity' => $entity,
            'form'   => $form->createView(),
            );

        return $this->render('NamecoSchedulerBundle:Establishment:new.html.twig', $renderParam);
	}
	
	
	
}
