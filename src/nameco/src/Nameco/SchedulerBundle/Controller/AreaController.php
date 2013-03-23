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
		$form = $this->createDeleteForm(null);
    	return $this->render('NamecoSchedulerBundle:Area:index.html.twig',
				array(
					'areas' => $areas,
					'delete_form' => $form->createView(),
					));
    }
	
	/**
     * @Route("/admin/area/new", name="admin_establishment_area_new")
	 */
	public function newAction()
	{
        $entity  = new Area();
		return $this->prepareForm($entity, 'new');
	}

	/**
	 * @Route("/admin/area/edit/{id}", name="admin_establishment_area_edit", requirements={"id"="\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NamecoSchedulerBundle:Area')->find($id);
		return $this->prepareForm($entity, 'edit');
	}

	/**
	 * @Route("/admin/area/delete", name="admin_establishment_area_delete")
	 * 
	 * @param type $id
	 */
	public function deleteAction(Request $request)
	{
        $form = $this->createDeleteForm(null);
        $form->bindRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('NamecoSchedulerBundle:Area')->find($form['id']->getData());
			if ($em->getRepository('NamecoSchedulerBundle:Establishment')->countArea($entity->getId()) == 0)
			{
				$em->remove($entity);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', '削除しました。');
			}
			else
			{
				$this->get('session')->getFlashBag()->add('error', '使用中のエリアは削除できません。');
			}
        }
		return $this->redirect($this->generateUrl('admin_establishment_area'));
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
        $form = $this->createForm(new AreaType(), $entity);
		$request = $this->getRequest();
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
           if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
				$this->get('session')->getFlashBag()->add('success', '保存しました');

                return $this->redirect($this->generateUrl('admin_establishment_area'));
            }
        }

		return $this->render('NamecoSchedulerBundle:Area:'.$template.'.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            ));
	}
}
