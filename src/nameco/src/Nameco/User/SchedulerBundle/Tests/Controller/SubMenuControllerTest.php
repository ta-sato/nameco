<?php
namespace Nameco\User\SchedulerBundle\Tests\Controller;

class SubMenuControllerTest extends BaseSchedulerControllerTest
{
	private $_client;
	
	public function setUp()
	{
		$this->initDatabase();
		$this->loadData();
		$this->_client = static::createClient();
		$this->login($this->_client, "test0", "testtest");
	}
	
	public function testGetScheduleMonthAction()
	{
		$client = $this->_client;
		$crawler = $client->request('GET', '/schedule/submenu/month', array('year' => date('Y'), 'month' => date('m')));
		$this->assertGreaterThan(0, $crawler->filter('ul.dropdown-menu')->children()->count());
	}
}
