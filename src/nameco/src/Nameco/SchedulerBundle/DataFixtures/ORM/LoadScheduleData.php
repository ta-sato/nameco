<?php

namespace Nameco\SchedulerBundle\DataFixtures\ORM;

use Nameco\SchedulerBundle\Entity\Schedule;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScheduleData extends AbstractFixture implements OrderedFixtureInterface
{
	private $container;

	public function __construct(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $em)
	{
		$date = new \DateTime();
		$timestamp = $date->getTimestamp();
		$date->setDate(date("Y", $timestamp), date("m", $timestamp), 1)->setTime(0, 0, 0);

		// 前月の予定
		$start = clone $date;
		$start->modify("-1 day");
		$end = clone $start;
		$end->setTime(23, 59, 59);
		$s = new Schedule();
		$s->setTitle("Title Prev");
		$s->setDetail("Detail Prev");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($em->merge($this->getReference('user-0')));
		$s->addUser($em->merge($this->getReference('user-0')));
		$em->persist($s);

		// 翌月の予定
		$start = clone $date;
		$start->modify("+1 month");
		$end = clone $start;
		$end->setTime(23, 59, 59);
		$s = new Schedule();
		$s->setTitle("Title Next");
		$s->setDetail("Detail Next");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($em->merge($this->getReference('user-0')));
		$s->addUser($em->merge($this->getReference('user-0')));
		$em->persist($s);

		// 今月頭の予定
		$start = clone $date;
		$end = clone $start;
		$end->setTime(1, 0, 0);
		$s = new Schedule();
		$s->setTitle("Title this month");
		$s->setDetail("Detail this month");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($em->merge($this->getReference('user-0')));
		$s->addUser($em->merge($this->getReference('user-0')));
		$em->persist($s);

		// 今月最後の予定
		$start = clone $date;
		$start->modify("+1 month")->modify("-1 day");
		$end = clone $start;
		$end->setTime(23, 59, 59);
		$s = new Schedule();
		$s->setTitle("Title this month");
		$s->setDetail("Detail this month");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($em->merge($this->getReference('user-0')));
		$s->addUser($em->merge($this->getReference('user-0')));
		$em->persist($s);

		$start = new \DateTime();
		$start->setTime(9, 0, 0);
		$end = clone $start;
		$end->modify('+2 hours');
		$nextStart = clone $start;
		$nextEnd = clone $end;
		$prevStart = clone $start;
		$prevEnd = clone $end;
		$nextStart->modify('+1 month');
		$nextEnd->modify('+1 month');
		$prevStart->modify('-1 month');
		$prevEnd->modify('-1 month');

		for ($i = 1; $i < 5; $i++)
		{
			$s = new Schedule();
			$s->setTitle("Title" . $i);
			$s->setDetail("Detail" . $i);
			$s->setStartDatetime($start);
			$s->setEndDatetime($end);
			$s->setOut(false);
			$s->setOwnerUser($em->merge($this->getReference('user-' . $i)));
			$s->addUser($em->merge($this->getReference('user-' . $i)));
			$em->persist($s);

			$s1 = new Schedule();
			$s1->setTitle("Title Next" . $i);
			$s1->setDetail("Detail Next" . $i);
			$s1->setStartDatetime($nextStart);
			$s1->setEndDatetime($nextEnd);
			$s1->setOut(false);
			$s1->setOwnerUser($em->merge($this->getReference('user-' . $i)));
			$s1->addUser($em->merge($this->getReference('user-' . $i)));
			$em->persist($s1);

			$s2 = new Schedule();
			$s2->setTitle("Title Prev" . $i);
			$s2->setDetail("Detail Prev" . $i);
			$s2->setStartDatetime($prevStart);
			$s2->setEndDatetime($prevEnd);
			$s2->setOut(false);
			$s2->setOwnerUser($em->merge($this->getReference('user-' . $i)));
			$s2->addUser($em->merge($this->getReference('user-' . $i)));
			$em->persist($s2);
		}

		$em->flush();
	}

	public function getOrder()
	{
		return 3;
	}
}
