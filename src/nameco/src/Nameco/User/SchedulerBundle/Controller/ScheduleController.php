<?php

namespace Nameco\User\SchedulerBundle\Controller;

use Nameco\User\SchedulerBundle\Entity\User;

use Nameco\User\SchedulerBundle\Entity\Schedule;
use Nameco\User\SchedulerBundle\Form\ScheduleType;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ScheduleController extends SchedulerBaseController
{
    /**
     * @Route("/schedule/month/", name="schedule_month")
     * @Route("/schedule/month/{id}", name="schedule_month_id")
     * @Route("/schedule/month/{year}/{month}", name="schedule_month_ym")
     * @Route("/schedule/month/{id}/{year}/{month}", name="schedule_month_id_ym")
     * @Template()
     */
    public function monthAction($id = null, $year = null, $month = null)
    {
        $user = null;
        if ($id == null)
        {
            $user = $this->getUser();
            $id = $user->getId();
        }
        else
        {
            $user = $this->getDoctrine()->getRepository('NamecoUserSchedulerBundle:User')->find($id);
            if (!$user)
            {
                return $this->redirect($this->generateUrl('schedule_month'));
            }
        }
        
        if ($year == null || $month == null)
        {
            $year  = date("Y");
            $month = date("m");
        }
        
        list($firstDay, $lastDay, $week, $dispDate) = $this->calcMonthRange($year, $month);
        
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('NamecoUserSchedulerBundle:Schedule')->getUserMonthSchedules($id, $firstDay, $lastDay);
        
        return array(
                'year'      => $year,
                'month'     => $month,
                'start'     => $firstDay, 
                'end'       => $lastDay,
                'week'      => $week,
                'schedules' => $result,
                'id'        => $id,
                'dispDate'  => $dispDate,
                'dispTargetLabel' => $user->getName());
    }
    
    /**
     * @Route("/schedule/new", name="schedule_new")
     * @Template()
     */
    public function newAction()
    {
        $schedule = new Schedule();
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $schedule->setStartDatetime($startDate);
        $schedule->setEndDatetime($endDate);
        $form = $this->createForm(new ScheduleType(), $schedule);
        return array('form' => $form->createView(), 'startDate' => $startDate, 'endDate' => $endDate);
    }
    
    /**
     * @Route("/schedule/create", name="schedule_create")
     * @Method("POST")
     * @Template("NamecoUserSchedulerBundle:Schedule:new.html.twig")
     * @param Request $request
     */
    public function createAction(Request $request)
    {
    	$em = $this->getDoctrine()->getEntityManager();
        $schedule = new Schedule();
        $form = $this->createForm(new ScheduleType(), $schedule);
        $form->bindRequest($request);
        
        $owner = $em->getRepository('NamecoUserSchedulerBundle:User')->find($this->getUser()->getId()); // TODO
        $schedule->setOwnerUser($owner);
        $schedule->addUser($owner);
        
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($schedule);
            $em->flush();
        }
        return array('form' => $form->createView(), 'startDate' => new \DateTime(), 'endDate' => new \DateTime());
    }
    
    /**
     * @Route("/schedule/selectedMonth", name="schedule_selected_month")
     * @Template()
     */
    public function selectedMonthAction()
    {
        $request = $this->getRequest();
        $linkParam['id'] = $request->request->get('idVal');
        $linkParam['year'] = $request->request->get('y');
        $linkParam['month'] = $request->request->get('m');
        // 表示対象にリダイレクト
        return $this->redirect($this->generateUrl('schedule_month_id_ym', $linkParam));
    }
}
