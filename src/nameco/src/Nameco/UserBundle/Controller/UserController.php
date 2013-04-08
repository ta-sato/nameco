<?php

namespace Nameco\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nameco\UserBundle\Entity\User;
use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;

class UserController extends Controller
{
	/**
	 * @Route("/admin/user/{page}", name="user", requirements={"page" = "\d+"}, defaults={"page" = "1"})
	 */
	public function indexAction($page)
	{
        $q          = $this->getDoctrine()->getRepository('NamecoUserBundle:User')->getUserListQuery();
        $paginator  = $this->get('knp_paginator');
        $users      = $paginator->paginate(
								            $q,
								            $page,
								            20
								        );
		return $this->render('NamecoUserBundle:User:index.html.twig',
			array('users' => $users));
	}
    
	/**
	 * @Route("/admin/user/new", name="user_new")
	 */
	public function newAction()
	{
		$user    = new User();
        $form    = $this->getForm(false, $user);
        $request = $this->getRequest();
		if ($request->getMethod() == 'POST')
		{
			if ($this->update($form, false))
			{
				$this->get('session')->getFlashBag()->add('success', '登録しました');
				return $this->redirect($this->generateUrl('user_edit', array('id' => $user->getId())));
			}
		}
		return $this->render('NamecoUserBundle:User:new.html.twig', array('form' => $form->createView()));	
	}

	/**
	 * @Route("/admin/user/edit/{id}", name="user_edit", requirements={"id"="\d+"})
	 */
	public function editAction($id)
	{
        $form    = $this->getForm(true, $this->getDoctrine()->getRepository('NamecoUserBundle:User')->find($id));
        $request = $this->getRequest();
		if ($request->getMethod() == 'POST')
		{
			if ($this->update($form, true))
			{
				$this->get('session')->getFlashBag()->add('success', '更新しました');
			}
		}
		return $this->render('NamecoUserBundle:User:edit.html.twig', array(
			'form' => $form->createView()));
	}

	/**
	 * @Route("/admin/user/remove/{id}", name="user_remove", requirements={"id"="\d+"})
	 */
	public function removeAction($id)
	{
		$user = $this->getDoctrine()->getRepository('NamecoUserBundle:User')->find($id);
		if ($user == null)
		{
			$this->get('session')->setFlash('error', '既に削除されています');
		}
		elseif ($user->getId() == $this->get('security.context')->getToken()->getUser()->getId())
		{
			$this->get('session')->setFlash('error', 'ログイン中は削除できません');	
		}
		else
		{
			$em = $this->getDoctrine()->getManager();
	 		$em->remove($user);
	        $em->flush();
	        $this->get('session')->setFlash('success', '削除しました');
		}
		return $this->redirect($this->generateUrl('user'));
	}

	private function getForm($edit, $user)
	{
		$form = $this->createFormBuilder($user)
			->add('family_name',    'text')
			->add('first_name',     'text')
			->add('kana_family',    'text')
			->add('kana_first',     'text')
			->add('email',          'email')
			->add('password',       'repeated', array(
										'type'            => 'password',
										'invalid_message' => 'パスワードが一致しません',
										'required'        => !$edit))
			->add('userRoles',     'entity', array(
										'class'    => 'NamecoUserBundle:Role',
										'property' => 'name',
										'multiple' => true
									))
			->add('isActive',       'checkbox', array('required' => false))
			->getForm();
		return $form;			
	}

	private function update($form, $edit)
	{
		$request = $this->getRequest();
		$form->bind($request);

		if (!$form->isValid())
		{
			return false;
		}
        $user = $form->getData();
        $pwd  = $form['password']->getData();
		if (isset($pwd))
		{
			$user->encodePassword($this, $pwd);
		}

		$em = $this->getDoctrine()->getManager();
		if (!$edit)
		{
			$em->persist($user);
		}
		
		$em->flush();
		return true;
	}

}