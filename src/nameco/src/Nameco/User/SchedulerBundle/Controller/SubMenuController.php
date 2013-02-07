<?php

namespace Nameco\User\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * サブメニューコントローラ
 * @author yu-ito
 */
class SubMenuController extends Controller
{
    /**
     * 月表示ユーザ選択
     * @Route("/schedule/submenu/month")
     * @Template()
     */
    public function scheduleMonthAction($year, $month)
    {
        $users = $this->getDoctrine()->getRepository('NamecoUserSchedulerBundle:User')->findAll();
        return array('users' => $users, 'year' => $year, 'month' => $month);
    }
    
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
