<?php

namespace Nameco\User\SchedulerBundle\Controller;

use Nameco\User\SchedulerBundle\Entity\User;
use Nameco\User\SchedulerBundle\Entity\Schedule;
use Nameco\User\SchedulerBundle\Form\ScheduleType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
                'userId'    => $id,
                'dispDate'  => $dispDate,
                'dispTargetLabel' => $user->getName());
    }
    
    /**
     * @Route("/schedule/new/{userId}/{year}/{month}/{day}", name="schedule_new")
     * @Template()
     */
    public function newAction(Request $request, $userId, $year, $month, $day)
    {
        if (!$request->isXmlHttpRequest())
        {
            return $this->redirect($this->generateUrl('schedule_month'));
        }
        $user = $this->getDoctrine()->getEntityManager()->getRepository('NamecoUserSchedulerBundle:User')->find($userId);
        if (!$user)
        {
            $user = $this->getUser();
        }
        $schedule = new Schedule();
        $schedule->addUser($user);
        $date = new \DateTime();
        $date->setDate($year, $month, $day);
        $schedule->setStartDatetime($date);
        $schedule->setEndDatetime($date);
        $form = $this->createForm(new ScheduleType(), $schedule);
        return array('form' => $form->createView(), 'startDate' => $date, 'endDate' => $date);
    }
    
    /**
     * @Route("/schedule/create", name="schedule_create")
     * @Method("POST")
     * @Template("NamecoUserSchedulerBundle:Schedule:new.html.twig")
     * @param Request $request
     */
    public function createAction(Request $request)
    {
        if (!$request->isXmlHttpRequest())
        {
            return $this->redirect($this->generateUrl('schedule_month'));
        }
        $em = $this->getDoctrine()->getEntityManager();
        $schedule = new Schedule();
        $form = $this->createForm(new ScheduleType(), $schedule);
        $form->bindRequest($request);
        
        $schedule->setOwnerUser($this->getUser());
        
        if ($this->checkEstablishment($form, $schedule, $em) && $form->isValid())
        {
            $em->persist($schedule);
            $em->flush();
            return $this->render('NamecoUserSchedulerBundle:Schedule:createSuccess.html.twig');
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
    
    public function checkEstablishment(Form $form, Schedule $schedule, $em)
    {
        if ($em->getRepository('NamecoUserSchedulerBundle:Establishment')->isBooking($schedule))
        {
            $form->get('establishment')->addError(new FormError("既に施設が予約されています"));
            return false;
        }
        return true;
    }
    
    
    /**
     * @Route("/schedule/users", name="schedule_users")
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getEntityManager()->getRepository('NamecoUserSchedulerBundle:User')->findAll();
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize($users, 'json');
        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}
