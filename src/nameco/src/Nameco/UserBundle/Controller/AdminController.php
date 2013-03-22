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
		$items = $this->get('nameco.menumanager')->getItems();
		if (count($items) == 0)
		{
			return $this->render('NamecoUserBundle:Admin:index.html.twig');
		}

		// 管理ページに最初に遷移してきた場合は先頭のアイテムにリダイレクトする
		$item = $items[0];
		return $this->redirect($this->generateUrl($item['path']));
	}
}

?>
