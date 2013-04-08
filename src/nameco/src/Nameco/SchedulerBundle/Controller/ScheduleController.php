<?php

namespace Nameco\SchedulerBundle\Controller;

use Nameco\UserBundle\Entity\User;
use Nameco\SchedulerBundle\Entity\Schedule;
use Nameco\SchedulerBundle\Form\ScheduleType;

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
     * @Route("/schedule/month/{id}", name="schedule_month_id", requirements={"id"="\d+"})
     * @Route("/schedule/month/{year}/{month}", name="schedule_month_ym", requirements={"year"="\d+", "month"="\d+"})
     * @Route("/schedule/month/{id}/{year}/{month}", name="schedule_month_id_ym", requirements={"id"="\d+", "year"="\d+", "month"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function monthAction($id = null, $year = null, $month = null)
    {
        $user = null;
        if ($id == null)
        {
            $user = $this->getUser();
        }
        else
        {
            $user = $this->getDoctrine()->getRepository('NamecoUserBundle:User')->find($id);
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

        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('NamecoSchedulerBundle:Schedule')->getUserMonthSchedules($user->getId(), $firstDay, $lastDay);

        $holidays = $em->getRepository('NamecoSchedulerBundle:Holiday')->getHolidays($firstDay, $lastDay);

        $users = $em->getRepository('NamecoUserBundle:User')->findAll();

        return array(
			'year'      => $year,
			'month'     => $month,
			'start'     => $firstDay,
			'end'       => $lastDay,
			'week'      => $week,
			'schedules' => $result,
			'user'      => $user,
			'dispDate'  => $dispDate,
			'users'     => $users,
            'holidays'  => $holidays,
			);
    }

	/**
     * @Route("/schedule/new/{userId}/{year}/{month}/{day}", requirements={"userId"="\d+", "year"="\d+", "month"="\d+", "day"="\d+"}, defaults={"establishmentId"=null})
     * @Route("/schedule/new/{userId}/{establishmentId}/{year}/{month}/{day}", requirements={"userId"="\d+", "establishmentId"="\d+", "year"="\d+", "month"="\d+", "day"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request, $userId, $establishmentId = null, $year, $month, $day)
    {
        if (!$request->isXmlHttpRequest())
        {
            return $this->redirect($this->generateUrl('schedule_month'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('NamecoUserBundle:User')->find($userId);
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
        if ($establishmentId)
        {
            $e = $em->getRepository('NamecoSchedulerBundle:Establishment')->find($establishmentId);
            if ($e) {
                $schedule->addEstablishment($e);
            }
        }
        $form = $this->createForm(new ScheduleType(), $schedule);
        return array('form' => $form->createView(), 'startDate' => $date, 'endDate' => $date);
    }

    /**
     * @Route("/schedule/create", name="schedule_create")
     * @Method("POST")
     * @Template("NamecoSchedulerBundle:Schedule:new.html.twig")
     * @param Request $request
     */
    public function createAction(Request $request)
    {
        if (!$request->isXmlHttpRequest())
        {
            return $this->redirect($this->generateUrl('schedule_month'));
        }
        $em = $this->getDoctrine()->getManager();
        $schedule = new Schedule();
        $form = $this->createForm(new ScheduleType(), $schedule);
        $form->bind($request);

        $schedule->setOwnerUser($this->getUser());

        if ($this->checkEstablishment($form, $schedule, $em) && $form->isValid())
        {
            $em->persist($schedule);
            $em->flush();
            return $this->render('NamecoSchedulerBundle:Schedule:createSuccess.html.twig');
        }
        return array('form' => $form->createView(), 'startDate' => new \DateTime(), 'endDate' => new \DateTime());
    }

    /**
     * @Route("/schedule/selectedMonth", name="schedule_selected_month")
     * @Method("POST")
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

    private function checkEstablishment(Form $form, Schedule $schedule, $em)
    {
        if ($em->getRepository('NamecoSchedulerBundle:Establishment')->isBooking($schedule))
        {
            $form->get('establishment')->addError(new FormError("既に施設が予約されています"));
            return false;
        }
        return true;
    }


    /**
     * @Route("/schedule/users", name="schedule_users")
     * @Method("GET")
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getManager()->getRepository('NamecoUserBundle:User')->findAll();
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize($users, 'json');
        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
