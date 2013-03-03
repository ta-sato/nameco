<?php

namespace Nameco\SchedulerBundle\Tests\Repository;

use Nameco\SchedulerBundle\Entity\Schedule;
use Nameco\SchedulerBundle\Tests\Controller\BaseSchedulerControllerTest;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EstablishmentRepositoryFunctionalTest extends BaseSchedulerControllerTest
{
	private $_em;

	public function setUp()
	{
		parent::setUp();
		$this->loadData(array(new LoadEstablishmentScheduleData()));
		$kernel = static::createKernel();
		$kernel->boot();
		$this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
	}

	public function testIsBooking()
	{
		$em = $this->_em;
		$repo = $em->getRepository('NamecoSchedulerBundle:Establishment');

		$start = new \DateTime();
		$start->setTime(9, 0, 0);
		$end = clone $start;
		$end->modify('+3 hour');

		$user = $em->getRepository('NamecoSchedulerBundle:User')->findOneByUsername('test0');
		$es = $repo->findAll();

		$s = new Schedule();
		$s->setTitle("Title");
		$s->setDetail("Detail");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($user);
		$s->addUser($user);

		// 施設:なし
		$result = $repo->isBooking($s);
		$this->assertFalse($result);

		// 単:かぶりなし
		$s->addEstablishment($es[2]);
		$result = $repo->isBooking($s);
		$this->assertFalse($result);
		$s->removeEstablishment($es[2]);

		// 施設:単:かぶり
		$s->addEstablishment($es[0]);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);
		$s->removeEstablishment($es[0]);

		// 施設:複:かぶりなし
		$s->addEstablishment($es[2]);
		$s->addEstablishment($es[3]);
		$result = $repo->isBooking($s);
		$this->assertFalse($result);
		$s->removeEstablishment($es[2]);
		$s->removeEstablishment($es[3]);

		// 施設:複:かぶり
		$s->addEstablishment($es[2]);
		$s->addEstablishment($es[1]);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);
		$s->removeEstablishment($es[1]);
		$s->removeEstablishment($es[2]);


		// 時間でのかぶりチェック 施設はかぶるように設定
		$s->addEstablishment($es[0]);
		$s->addEstablishment($es[2]);

		// 時間:かぶりなし
		$tmpStart = clone $start;
		$tmpStart->modify('-1 hour');
		$tmpEnd = clone $start;
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertFalse($result);

		$tmpStart = clone $end;
		$tmpEnd = clone $end;
		$tmpEnd->modify('+1 hour');
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertFalse($result);

		// 時間:かぶりあり
		$tmpStart = clone $end;
		$tmpStart->modify("-1 second");
		$tmpEnd = clone $end;
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);

		$tmpStart = clone $start;
		$tmpEnd = clone $start;
		$tmpEnd->modify("+1 second");
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);

		$tmpStart = clone $start;
		$tmpStart->modify("-1 second");
		$tmpEnd = clone $end;
		$tmpEnd->modify("+1 second");
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);

		$tmpStart = clone $start;
		$tmpStart->modify("+1 second");
		$tmpEnd = clone $end;
		$tmpEnd->modify("-1 second");
		$s->setStartDatetime($tmpStart);
		$s->setEndDatetime($tmpEnd);
		$result = $repo->isBooking($s);
		$this->assertTrue($result);
	}
}

class LoadEstablishmentScheduleData extends AbstractFixture implements OrderedFixtureInterface
{
	private $container;

	public function __construct(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $em)
	{
		$start = new \DateTime();
		$start->setTime(9, 0, 0);
		$end = clone $start;
		$end->modify('+3 hour');

		$user_ref = $this->getReference('user-0');
		$establishment_ref0 = $this->getReference('establishment-0');
		$establishment_ref1 = $this->getReference('establishment-1');

		$s = new Schedule();
		$s->setTitle("Title");
		$s->setDetail("Detail");
		$s->setStartDatetime($start);
		$s->setEndDatetime($end);
		$s->setOut(false);
		$s->setOwnerUser($em->merge($user_ref));
		$s->addUser($em->merge($user_ref));
		$s->addEstablishment($em->merge($establishment_ref0));
		$s->addEstablishment($em->merge($establishment_ref1));

		$em->persist($s);
		$em->flush();
	}

	public function getOrder()
	{
		return 1000;
	}
}