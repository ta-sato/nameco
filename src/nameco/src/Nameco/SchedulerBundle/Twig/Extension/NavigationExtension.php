<?php

namespace Nameco\SchedulerBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use DateTime;

class NavigationExtension extends \Twig_Extension
{
	private $generator;

	public function __construct(UrlGeneratorInterface $generator)
	{
		$this->generator = $generator;
	}

    public function getFilters()
    {
        return array(
            'diplayDate' => new \Twig_Filter_Method($this, 'diplayDateFilter'),
        	'nextPath' => new \Twig_Filter_Method($this, 'nextPathFilter'),
        	'prevPath' => new \Twig_Filter_Method($this, 'prevPathFilter'),
        	'monthPath' => new \Twig_Filter_Method($this, 'monthPahtFilter')
        );
    }


    /**
     * 表示日生成
     * @param \DateTime $dispDate
     * @return string
     */
    public function diplayDateFilter(\DateTime $dispDate)
    {
    	// 月/週表示で取得設定するパラメータ切り替え
		$diplayDateFormat = 'Y年m月';
		//if ($dispDateType === 'week')
		//{
		//	$diplayDateFormat = 'Y年m月d日';
		//}
		$date = $dispDate->format($diplayDateFormat); // 表示日
		return $date;
    }

    /**
     * 次のURL生成
     * @param \DateTime $dispDate
     * @param unknown_type $monthRoutName
     * @param unknown_type $id
     * @return unknown
     */
    public function nextPathFilter(\DateTime $dispDate, $monthRoutName, $id)
    {
		//$routeName = $monthRoutName;
		$modify = 'month';

		$date = new \DateTime($dispDate->format('Y-m-d'));
		// 表示日付を基に次の日付取得
		$date->modify('+1 '.$modify);
		return $this->generateMonthURL($date, $monthRoutName, $id);
    }

    /**
     * 前のURL生成
     * @param \DateTime $dispDate
     * @param unknown_type $monthRoutName
     * @param unknown_type $id
     * @return unknown
     */
    public function prevPathFilter(\DateTime $dispDate, $monthRoutName, $id)
    {
    	//$routeName = $monthRoutName;
    	$modify = 'month';

    	$date = new \DateTime($dispDate->format('Y-m-d'));
    	// 表示日付を基に前の日付取得
    	$date->modify('-1 '.$modify);
    	return $this->generateMonthURL($date, $monthRoutName, $id);
    }

    /**
     * 月表示URL生成
     * @param \DateTime $dispDate
     * @param unknown_type $RoutName
     * @param unknown_type $id
     * @return unknown
     */
    public function monthPahtFilter(\DateTime $dispDate, $routName, $id)
    {
    	return $this->generateMonthURL($dispDate, $routName, $id);
    }

    /**
     * URL生成
     * @param \DateTime $date
     * @param unknown_type $routName
     * @param unknown_type $id
     */
    private function generateMonthURL(\DateTime $date, $routName, $id)
    {
    	$linkParam['id']       = $id;
    	$linkParam['year']     = $date->format('Y');
    	$linkParam['month']    = $date->format('m');
		return $nextDateUrl = $this->generator->generate($routName, $linkParam);
    }

    public function getName()
    {
        return 'navigation_extension';
    }
}