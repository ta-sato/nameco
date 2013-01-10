<?php

namespace Nameco\Admin\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nameco\Admin\UserBundle\Entity\User;
use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;

class UserController extends Controller
{
	/**
	 * @Route("/user/new")
	 */
	public function indexAction(Request $request)
	{
		$user = new User();

		$form = $this->createFormBuilder($user)
			->add('familly_name',     'text', array('property_path' => false, 'required' => true))
			->add('first_name',       'text', array('property_path' => false, 'required' => true))
			->add('kana_familly',     'text', array('property_path' => false, 'required' => true))
			->add('kana_first',       'text', array('property_path' => false, 'required' => true))
			->add('email',            'email')
			->add('password',         'password')
			->add('confirm',          'password', array('property_path' => false, 'required' => true))
			->addValidator(new CallbackValidator(function($form)
			{
				// パスワード
				if($form['password']->getData() != $form['confirm']->getData())
				{
					$form['confirm']->addError(new FormError('パスワードが一致しません'));
				}
				// メール
				$em = $this->getDoctrine()->getEntityManager();

				$user_repo = $em->getRepository('NamecoAdminUserBundle:User');
				$exsist = $user_repo->findOneBy(array('email' => $form['email']->getData())) != null;
				if ($exsist)
				{
					$form['email']->addError(new FormError('既に登録されています'));
				}
			}))
			->getForm();

		if ($request->getMethod() == 'POST')
		 {
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$user = $form->getData();
				$user->setKana($form['kana_familly']->getData() . ' ' . $form['kana_first']->getData());
				$user->setName($form['familly_name']->getData() . ' ' . $form['first_name']->getData());
				$user->setEnabled(true);

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
			}
		}

		return $this->render('NamecoAdminUserBundle:User:new.html.twig', array(
				'form' => $form->createView(),
		));	}

}