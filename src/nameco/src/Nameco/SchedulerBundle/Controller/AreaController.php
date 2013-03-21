<?php

namespace Nameco\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Nameco\SchedulerBundle\Form\AreaType;
use Nameco\SchedulerBundle\Entity\Area;

/**
 * 施設エリア
 */
class AreaController extends Controller
{
    /**
     * 施設エリア一覧
     * @Route("/admin/area/", name="admin_establishment_area")
     */
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
		
        $areas = $em->getRepository('NamecoSchedulerBundle:Area')->findAll();

    	return $this->render('NamecoSchedulerBundle:Area:index.html.twig',
				array(
					'areas' => $areas,
					));
    }
	
	/**
     * @Route("/admin/area/new", name="admin_establishment_area_new")
	 */
	public function newAction(Request $request)
	{
        $entity  = new Area();
        $form    = $this->createForm(new AreaType(), $entity);
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_establishment_area'));
            }
        }

        $renderParam = array(
            'entity' => $entity,
            'form'   => $form->createView(),
            );

        return $this->render('NamecoSchedulerBundle:Area:new.html.twig', $renderParam);
	}
}
