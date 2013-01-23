<?php

namespace Nameco\User\EstablishmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EstablishmentController extends Controller
{
    /**
     * @Route("/establishment/month/{id}/{year}/{month}")
     * @Template()
     */
    public function monthAction($id, $year, $month)
    {
		// 月の初日が日曜でなければ日曜までずらす
    	$firstDay = new \DateTime($year.'-'.$month.'-1');
    	$firstDay->modify('-'.$firstDay->format('w').' day');

    	// 月の最終日が土曜でなければ土曜までずらす
    	$lastDay = new \DateTime('last day of '.$year.'-'.$month);
    	$diffWeek = 6 - $lastDay->format('w');
    	$lastDay->modify('+'.$diffWeek.' day');
    	// DBからの検索用最終日を作成(カレンダーの最終日+1日)
    	$searchLastDay = clone $lastDay;
    	$searchLastDay->modify("+1 day");


    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT s FROM NamecoUserEstablishmentBundle:Schedule s JOIN NamecoUserEstablishmentBundle:Establishment e
    			WHERE e.id = :id AND s.startDatetime >= :firstDay AND s.startDatetime < :lastDay ORDER BY s.startDatetime ASC')
    	->setParameter('id', $id)
    	->setParameter('firstDay', $firstDay)
    	->setParameter('lastDay', $searchLastDay);

    	$result = $query->getResult();

    	return array('firstDay' => $firstDay, 'lastDay' => $lastDay, 'result' => $result);
    }

    /**
     * $year	int	求めたい日付の年を指定
     * $month	int	求めたい日付の月を指定
     * $week	int	第n週か。第1週なら1、第3週なら3を指定
     * $day		int	求めたい曜日。0〜6までの数字で指定
     */
    private function getWeekOfDay($year, $month, $week, $day) {
    	// 1.指定した年月の最初の曜日を取得
    	$firstDay = date("w", mktime(0, 0, 0, $month, 1, $year));

    	// 2.求めたい曜日の第1週の日付けを計算する
    	$day = $day - $firstDay + 1;
    	if($day <= 0) $day += 7;

    	// 3.n週まで1週間を足す
    	$day += 7 * ($week - 1);

    	// 4.結果を返す
    	return date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
    }


    function getLastSunday($year, $month, $day) {
    	$today = mktime(0, 0, 0, $month, $day, $year);
    	$arr = getdate($today);
    	$ww = $arr['wday'];      //指定日の曜日番号
    	return $today - $ww * 24 * 60 * 60;  //その週の日曜日のUNIX TIME
    }
}
