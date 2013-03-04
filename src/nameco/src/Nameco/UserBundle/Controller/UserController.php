<?php

namespace Nameco\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nameco\User\SchedulerBundle\Entity\User;
use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;

class UserController extends Controller
{
	/**
	 * @Route("/user/new", name="user_new")
	 */
	public function indexAction(Request $request)
	{
		$user = new User();

		$form = $this->createFormBuilder($user)
			->add('family_name',   'text')
			->add('first_name',     'text')
			->add('kana_family',   'text')
			->add('kana_first',     'text')
			->add('email',          'email')
			->add('password',       'repeated', array(
									'type'            => 'password',
									'invalid_message' => 'パスワードが一致しません'))
			->getForm();

		$sucsess = false;

		if ($request->getMethod() == 'POST')
		 {
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$user = $form->getData();
//				$user->build(
//						$form['email']->getData(),
//						$form['password']->getData(),
//						$form['family_name']->getData(),
//						$form['first_name']->getData(),
//						$form['kana_family']->getData(),
//						$form['kana_first']->getData()
//						);
//				$user->setKana($form['kana_family']->getData() . ' ' . $form['kana_first']->getData());
//				$user->setName($form['family_name']->getData() . ' ' . $form['first_name']->getData());
//				$user->setEnabled(true);

				// パスワードハッシュ化
				$passward = $form['password']->getData();
				$factory  = $this->get('security.encoder_factory');
				$encoder  = $factory->getEncoder($user);
				$hashedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
				$user->setPassword($hashedPassword);

				// usernameを@前に設定
				$mail = preg_split('/@/', $form['email']->getData());
				$user->setUsername($mail[0]);

				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($user);
   				$em->flush();

   				$sucsess = true;
			}
		}

		return $this->render('NamecoUserBundle:User:new.html.twig', array(
				'form'    => $form->createView(),
				'success' => $sucsess
		));	}

}