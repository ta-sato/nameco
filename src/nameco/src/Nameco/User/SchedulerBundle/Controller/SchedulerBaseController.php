<?php

namespace Nameco\User\SchedulerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class SchedulerBaseController extends Controller
{
    /**
     * 月表示に必要な値の計算
     * @param $year 年
     * @param $month 月
     * @return list(表示開始日, 表示終了日, 週数, 表示年月)
     */
    protected function calcMonthRange($year, $month)
    {
        // 月の初日が日曜でなければ日曜までずらす
        $firstDay = new \DateTime($year.'-'.$month.'-1');
        $firstDay->modify('-' .($firstDay->format('w') -1) .' day');
        
        // 月の最終日が土曜でなければ土曜までずらす
        $lastDay = new \DateTime('last day of '.$year.'-'.$month);
        $diffWeek = 6 - $lastDay->format('w') + 1;
        $lastDay->modify('+'.$diffWeek.' day');
        
        $diff = $firstDay->diff($lastDay);
        $week = (intval($diff->format( '%a' )) + 1) / 7;
        
        return array($firstDay, $lastDay, $week, new \DateTime($year.'-'.$month.'-1'));
    }
}
