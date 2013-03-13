<?php

namespace Nameco\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of AdminController
 *
 * @author a-namiki
 */
class AdminController extends Controller
{
	/**
	* @Route("/admin", name="admin")
	*/
	public function indexAction()
	{
		return $this->render('NamecoUserBundle:Admin:index.html.twig');
			    
	}
}

?>
