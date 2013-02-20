<?php

namespace Nameco\User\SchedulerBundle\Tests\Repository;

use Nameco\User\SchedulerBundle\DataFixtures\ORM\LoadScheduleData;

use Nameco\User\SchedulerBundle\Tests\Controller\BaseSchedulerControllerTest;

class ScheduleRepositoryFunctionalTest extends BaseSchedulerControllerTest
{
	private $_em;
	
	public function setUp()
	{
		parent::setUp();
		$this->loadData(array(new LoadScheduleData()));
		$kernel = static::createKernel();
		$kernel->boot();
		$this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
	}
	
	public function testGetUserMonthSchedules()
	{
		$start = new \DateTime();
		$timestamp = $start->getTimestamp();
		$start->setDate(date("Y", $timestamp), date("m", $timestamp), 1)->setTime(0, 0, 0);
		$end = clone $start;
		$end->modify("+1 month");
		
		$user0 = $this->_em->getRepository('NamecoUserSchedulerBundle:User')->findOneByUsername("test0");
		$results = $this->_em->getRepository('NamecoUserSchedulerBundle:Schedule')
				->getUserMonthSchedules($user0->getId(), $start, $end);
		
		$this->assertEquals(count($results), 2);
	}
}
