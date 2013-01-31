<?php
namespace Nameco\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
	/**
	 * @Route("login")
	 */
	public function loginAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		
		// ログインエラーがあれば、ここで取得
		$error_message = null;
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
		{
			$error_message = 'ユーザ名またはパスワードが違います';
		}
		else
		{
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		}
		
		return $this->render('NamecoSecurityBundle:Security:login.html.twig', array(
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error'         => $error,
		));
	} 	

}