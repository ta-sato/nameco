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
	 * @Route("/user/{page}", name="user", requirements={"page" = "\d+"}, defaults={"page" = "1"})
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
	 * @Route("/user/new", name="user_new")
	 */
	public function newAction($id = null)
	{
		$form = $this->getForm(false, new User());
		$success = false;
		if ($this->getRequest()->getMethod() == 'POST')
		{
			$success = $this->update($form, false);
		}
		return $this->render('NamecoUserBundle:User:new.html.twig', array(
				'form'    => $form->createView(),
				'success' => $success
		));	
	}

	/**
	 * @Route("/user/edit/{id}", name="user_edit")
	 */
	public function editAction($id)
	{
		$form = $this->getForm(true, $this->getDoctrine()->getRepository('NamecoUserBundle:User')->find($id));
		$success = false;
		if ($this->getRequest()->getMethod() == 'POST')
		{
			$success = $this->update($form, true);
		}
		return $this->render('NamecoUserBundle:User:edit.html.twig', array(
				'form'    => $form->createView(),
				'success' => $success
		));	
	}

	private function getForm($edit, $user)
	{
		$form = $this->createFormBuilder($user)
			->add('family_name',    'text')
			->add('first_name',     'text')
			->add('kana_family',    'text')
			->add('kana_first',     'text')
			->add('email',          'email', array('read_only' => $edit))
			->add('password',       'repeated', array(
										'type'            => 'password',
										'invalid_message' => 'パスワードが一致しません',
										'required'        => !$edit))
			->add('userRoles',     'entity', array(
										'class'    => 'NamecoUserBundle:Role',
										'property' => 'name',
										'multiple' => true
									))
			->getForm();
		return $form;			
	}

	private function update($form, $edit)
	{
		$request = $this->getRequest();
		$form->bindRequest($request);

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

		$em = $this->getDoctrine()->getEntityManager();
		if (!$edit)
		{
			$em->persist($user);
		}
		
		$em->flush();
		return true;
	}

}