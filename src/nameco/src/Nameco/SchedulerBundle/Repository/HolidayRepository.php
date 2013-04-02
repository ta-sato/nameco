<?php

namespace Nameco\SchedulerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Nanaweb\JapaneseHolidayBundle\JapaneseHoliday\JapaneseHoliday;

require __DIR__ . '/../../../../vendor/nanaweb/japaneseHoliday-bundle/JapaneseHoliday/JapaneseHoliday.php';

class HolidayRepository extends EntityRepository
{
    public function getHolidays($firstDay, $lastDay)
    {
        $japaneseHoliday = new JapaneseHoliday();

		$retval = array();
        foreach (new \DatePeriod($firstDay, new \DateInterval('P1D'), $lastDay->modify('+2 day')) as $day) {
            $key = $day->format('Y-m-d');
            $retval[$key] = $japaneseHoliday->getHolidayName($key);
        }

        return $retval;
    }
}
