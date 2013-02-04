<?php

namespace Nameco\User\EstablishmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SubMenuController extends Controller
{
	/**
	 * @Route("/establishment/submenu")
	 * @Template()
	 */
	public function mainAction(Request $request, $year = null, $month = null)
	{
		$areas = $this->getDoctrine()
			->getRepository('NamecoUserEstablishmentBundle:Area')
			->findAll();

// 		foreach ($area as $elem) {
// 		}

		return $this->render('NamecoUserEstablishmentBundle:SubMenu:index.html.twig', array(
				'areas' => $areas,
				'year' => $year,
				'month' => $month
			));
	}
}
