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

        $holidays = $em->getRepository('NamecoSchedulerBundle:Holiday')->getHolidays($firstDay, $lastDay);

    	// 施設選択用アイテム取得
        $areas = $em->getRepository('NamecoSchedulerBundle:Area')->findEstablishmentArea();

    	return $this->render('NamecoSchedulerBundle:Establishment:month.html.twig',
				array(
					'start'           => $firstDay,
					'end'             => $lastDay,
					'week'            => $week,
					'schedules'       => $result,
					'dispDate'        => $dispDate,
					'year'            => $year,
					'month'           => $month,
					'estab'			  => $estab,
					'areas'           => $areas,
					'user'            => $user = $this->getUser(),
                    'holidays'        => $holidays,
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
     * 施設エリア一覧
     * @Route("/admin/establishment", name="admin_establishment")
     */
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
		
        $establishments = $em->getRepository('NamecoSchedulerBundle:Establishment')->findAll();
		$form = $this->createDeleteForm(null);
    	return $this->render('NamecoSchedulerBundle:Establishment:index.html.twig',
				array(
					'establishments' => $establishments,
					'delete_form' => $form->createView(),
					));
    }

//	/**
//     * @Route("/admin/establishment/new", name="admin_establishment_new")
//	 */
//	public function newAction(Request $request)
//	{
//        $entity  = new Establishment();
//        $form    = $this->createForm(new EstablishmentType(), $entity);
//        if ($request->isMethod('POST'))
//        {
//            $form->bind($request);
//           if ($form->isValid()) {
//                $em = $this->getDoctrine()->getEntityManager();
//                $em->persist($entity);
//                $em->flush();
//                
//                return $this->redirect($this->generateUrl('admin_establishment'));
//            }
//        }
//
//        $renderParam = array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//            );
//
//        return $this->render('NamecoSchedulerBundle:Establishment:new.html.twig', $renderParam);
//	}
//	
	
	/**
     * @Route("/admin/establishment/new", name="admin_establishment_new")
	 */
	public function newAction()
	{
        $entity  = new Establishment();
		return $this->prepareForm($entity, 'new');
	}

	/**
	 * @Route("/admin/establishment/edit/{id}", name="admin_establishment_edit", requirements={"id"="\d+"})
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NamecoSchedulerBundle:Establishment')->find($id);
		return $this->prepareForm($entity, 'edit');
	}

	/**
	 * @Route("/admin/establishment/delete", name="admin_establishment_delete")
	 */
	public function deleteAction(Request $request)
	{
        $form = $this->createDeleteForm(null);
        $form->bindRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('NamecoSchedulerBundle:Establishment')->find($form['id']->getData());
			$em->remove($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add('success', '削除しました。');
        }
		return $this->redirect($this->generateUrl('admin_establishment'));
	}

	private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
	
	protected function prepareForm($entity, $template)
	{
        $form = $this->createForm(new EstablishmentType(), $entity);
		$request = $this->getRequest();
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
				$this->get('session')->getFlashBag()->add('success', '保存しました');

                return $this->redirect($this->generateUrl('admin_establishment'));
            }
        }

		return $this->render('NamecoSchedulerBundle:Establishment:'.$template.'.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            ));
	}
	
}
