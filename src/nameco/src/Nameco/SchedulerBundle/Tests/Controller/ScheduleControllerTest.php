<?php

namespace Nameco\SchedulerBundle\Tests\Controller;

use Nameco\SchedulerBundle\Entity\Schedule;

use Nameco\SchedulerBundle\DataFixtures\ORM\LoadScheduleData;

class ScheduleControllerTest extends BaseSchedulerControllerTest
{
	private $_client;
	private $_em;
	private $_loginUser;

	public function setUp()
	{
		$this->initDatabase();
		$this->loadData(array(new LoadScheduleData()));
		$this->_client = static::createClient();
		$this->login($this->_client, "test0", "testtest");

		$this->_em = $this->_client->getContainer()->get('doctrine')->getEntityManager();
		$this->_loginUser = $this->_em->getRepository('NamecoSchedulerBundle:User')->findOneByUsername("test0");
	}

	public function testMonth()
	{
		$client = $this->_client;

		$container = $client->getContainer();
		$em = $this->_em;

		$repo = $em->getRepository('NamecoSchedulerBundle:User');
		$login = $this->_loginUser;
		$other = $repo->findOneByUsername('test1');

		$crawler = $client->request('GET', '/schedule/month/');
		$this->assertEquals($login->getName(), $crawler->filter('div#navigation_left')->children()->last()->text());
		$this->assertEquals(date('Y') . '年' . date('m') . '月', $crawler->filter('div#navigation_left>span')->first()->text());
		$this->assertGreaterThan(0, $crawler->filter('ul#schedules')->children()->count());

		$date = new \DateTime();
		$date->modify("-1 month");
		$year = date('Y', $date->getTimestamp());
		$month = date('m', $date->getTimestamp());

		$crawler = $client->request('GET', '/schedule/month/' . $year . '/' . $month);
		$this->assertEquals($login->getName(), $crawler->filter('div#navigation_left')->children()->last()->text());
		$this->assertEquals($year . '年' . $month . '月', $crawler->filter('div#navigation_left>span')->first()->text());
		$this->assertGreaterThan(0, $crawler->filter('ul#schedules')->children()->count());

		$crawler = $client->request('GET', '/schedule/month/' . $other->getId());
		$this->assertEquals($other->getName(), $crawler->filter('div#navigation_left')->children()->last()->text());
		$this->assertEquals(date('Y') . '年' . date('m') . '月', $crawler->filter('div#navigation_left>span')->first()->text());
		$this->assertGreaterThan(0, $crawler->filter('ul#schedules')->children()->count());

		$crawler = $client->request('GET', '/schedule/month/' . $other->getId() . '/' . $year . '/' . $month);
		$this->assertEquals($other->getName(), $crawler->filter('div#navigation_left')->children()->last()->text());
		$this->assertEquals($year . '年' . $month . '月', $crawler->filter('div#navigation_left>span')->first()->text());
		$this->assertGreaterThan(0, $crawler->filter('ul#schedules')->children()->count());

		// 存在しないユーザ
		$client->request('GET', '/schedule/month/10000');
		$this->assertEquals('/schedule/month/', $client->getResponse()->getTargetUrl());
	}

	public function testNew()
	{
		$client = $this->_client;
		$container = $client->getContainer();

		$em = $this->_em;

		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$user = $this->_loginUser;
		$id = $user->getId();
		$id2 = $em->getRepository('NamecoSchedulerBundle:User')->findOneByUsername('test1')->getId();

		$est = $em->getRepository('NamecoSchedulerBundle:Establishment')->findOneByName('Establishment_0');
		$estId = $est->getId();

		$crawler = $client->request('GET', '/schedule/new/' . $id2 . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$this->assertEquals($year . '/' . $month . '/' . $day, $crawler->filter("a#btn-startDate")->attr('data-date'));
		$this->assertEquals('', $crawler->filter('input#schedule_title')->text());
		$this->assertEquals('', $crawler->filter('textarea#schedule_detail')->text());
		$this->assertEquals(null, $crawler->filter('input#schedule_out')->attr('checked'));
		$this->assertEquals(1, $crawler->filter('ul#schedule_user')->children()->count());
		$this->assertEquals($id2, $crawler->filter('ul#schedule_user')->children()->first()->filter('input[type=hidden]')->attr('value'));
		$this->assertEquals(0, $crawler->filter('select#schedule_establishment')->children()->filter('option[selected=selected]')->count());

		$crawler = $client->request('GET', '/schedule/new/10000/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$this->assertEquals($year . '/' . $month . '/' . $day, $crawler->filter("a#btn-startDate")->attr('data-date'));
		$this->assertEquals('', $crawler->filter('input#schedule_title')->text());
		$this->assertEquals('', $crawler->filter('textarea#schedule_detail')->text());
		$this->assertEquals(null, $crawler->filter('input#schedule_out')->attr('checked'));
		$this->assertEquals(1, $crawler->filter('ul#schedule_user')->children()->count());
		$this->assertEquals($id, $crawler->filter('ul#schedule_user')->children()->first()->filter('input[type=hidden]')->attr('value'));
		$this->assertEquals(0, $crawler->filter('select#schedule_establishment')->children()->filter('option[selected=selected]')->count());

		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $estId . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$this->assertEquals($year . '/' . $month . '/' . $day, $crawler->filter("a#btn-startDate")->attr('data-date'));
		$this->assertEquals('', $crawler->filter('input#schedule_title')->text());
		$this->assertEquals('', $crawler->filter('textarea#schedule_detail')->text());
		$this->assertEquals(null, $crawler->filter('input#schedule_out')->attr('checked'));
		$this->assertEquals(1, $crawler->filter('ul#schedule_user')->children()->count());
		$this->assertEquals($id, $crawler->filter('ul#schedule_user')->children()->first()->filter('input[type=hidden]')->attr('value'));
		$this->assertEquals(1, $crawler->filter('select#schedule_establishment')->children()->filter('option[selected=selected]')->count());

		$this->assertEquals('', '');

		// Ajaxじゃない
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day);
		$this->assertEquals('/schedule/month/', $client->getResponse()->getTargetUrl());
	}

	public function testCreate()
	{
		$client = $this->_client;
		$container = $client->getContainer();

		$em = $this->_em;

		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$user = $this->_loginUser;
		$id = $user->getId();
		$est = $em->getRepository('NamecoSchedulerBundle:Establishment')->findOneByName('Establishment_0');
		$estId = $est->getId();
		$beforeCount = count($em->getRepository('NamecoSchedulerBundle:Schedule')->findAll());

		// 正常登録
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
				$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array($estId)
		));

		$this->assertTrue(strpos($client->getResponse()->getContent(), 'location') !== false);
		$this->assertGreaterThan($beforeCount, count($em->getRepository('NamecoSchedulerBundle:Schedule')->findAll()));

		// 開始日付不正
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => '',
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("日付の形式が違います")')->count());


		// 終了日付不正
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => '',
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("日付の形式が違います")')->count());

		// 日付不正
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '1',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '0',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("開始日時が終了日時以降に設定されています")')->count());

		// タイトルなし
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => '',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("必須項目です")')->count());

		// タイトルオーバー
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'titletitletitletitletitletitletitletitletitletitle1',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array($estId)
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("文字以内で入力してください")')->count());

		// 詳細なし
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => '',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("必須項目です")')->count());

		// 詳細オーバー
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildetaildeta',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("文字以内で入力してください")')->count());

		// ユーザなし
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$schedule_form->remove('schedule[user][0]');
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array(),
				'schedule[establishment]' => array()
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("参加者を選んでください")')->count());

		// 既に施設が予約されている
		$crawler = $client->request('GET', '/schedule/new/' . $id . '/' . $year . '/' . $month . '/' . $day, array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
		$schedule_form = $crawler->selectButton('submit')->form();
		$client->setServerParameters(array('HTTP_X-Requested-With' => 'XMLHttpRequest')); // Ajax設定
		$crawler = $client->submit($schedule_form, array(
				'schedule[startDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[startDateTime][time][hour]' => '0',
				'schedule[startDateTime][time][minute]' => '0',
				'schedule[endDateTime][date]' => $year . '/' . $month . '/' . $day,
				'schedule[endDateTime][time][hour]' => '1',
				'schedule[endDateTime][time][minute]' => '0',
				'schedule[title]' => 'title',
				'schedule[detail]' => 'detail',
				'schedule[out]' => false,
				'schedule[user]' => array($id),
				'schedule[establishment]' => array($estId)
		));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("既に施設が予約されています")')->count());

 		// Ajaxじゃない
 		$client->setServerParameters(array());
 		$crawler = $client->request('POST', '/schedule/create');
 		$this->assertEquals('/schedule/month/', $client->getResponse()->getTargetUrl());
	}

	public function testSelectedMonth()
	{
		$client = $this->_client;
		$id = $this->_loginUser->getId();
		$year = date('Y');
		$month = date('m');
		$client->request('POST', '/schedule/selectedMonth', array('idVal' => $id, 'y' => $year, 'm' => $month));
		$this->assertEquals('/schedule/month/' . $id . '/' . $year . '/' . $month, $client->getResponse()->getTargetUrl());
	}

	public function testGetUsers()
	{
		$client = $this->_client;
		$container = $client->getContainer();

		$crawler = $client->request('GET', '/schedule/users');
		$this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

		$serializer = $container->get('jms_serializer');
		$data = $serializer->deserialize($client->getResponse()->getContent(), 'ArrayCollection<Nameco\SchedulerBundle\Entity\User>', 'json');
		$this->assertGreaterThan(0, count($data));
		$this->assertTrue($data[0]->getId() != null);
	}
}