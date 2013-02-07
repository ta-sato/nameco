<?php
namespace Nameco\User\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
	/**
	 * @Route("login")
	 * @Template()
	 */
	public function loginAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		

		$error = null;
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
		{
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR)->message;
		}
		else if ($session->has(SecurityContext::AUTHENTICATION_ERROR))
		{
			$error = 'ユーザ名またはパスワードが違います';
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}
		
		return array(
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error'         => $error);
	} 	

}