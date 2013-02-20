<?php

namespace Nameco\User\SchedulerBundle\Tests\Controller;

use Nameco\User\SchedulerBundle\DataFixtures\ORM\LoadScheduleData;

use Nameco\User\SchedulerBundle\DataFixtures\ORM\LoadUserData;
use Nameco\User\SchedulerBundle\DataFixtures\ORM\LoadEstablishmentData;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseSchedulerControllerTest extends WebTestCase
{
	public function setUp()
	{
		$this->initDatabase();
	}
	
	protected function login($client, $username, $password)
	{
		$crawler    = $client->request('GET', '/login');
		$login_form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($login_form, array(
				'_username'      => $username,
				'_password'      => $password,
		));
	}
	
	protected function logout($client)
	{
		$client->request('GET', '/logout');
	}
	
	protected function initDatabase()
	{
		$client = static::createClient();
		$container = $client->getContainer();
		$em = $container->get('doctrine')->getEntityManager();
		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
	
		$classes = array(
				$em->getClassMetadata('Nameco\User\SchedulerBundle\Entity\Area'),
				$em->getClassMetadata('Nameco\User\SchedulerBundle\Entity\Establishment'),
				$em->getClassMetadata('Nameco\User\SchedulerBundle\Entity\Schedule'),
				$em->getClassMetadata('Nameco\User\SchedulerBundle\Entity\User'),
		);
		$tool->dropDatabase();
		$tool->createSchema($classes);
	}
	
	protected function loadData($loadFixtures = array())
	{
		$client = static::createClient();
		$container = $client->getContainer();
		$em = $container->get('doctrine')->getEntityManager();
		
		$loader = new Loader($container);
		$loader->addFixture(new LoadEstablishmentData());
		$loader->addFixture(new LoadUserData($container));
		if ($loadFixtures)
		{
			foreach ($loadFixtures as $f)
			{
				$loader->addFixture($f);
			}
		}
		$fixtures = $loader->getFixtures();

		$purger = new ORMPurger($em);
		$purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
		$executor = new ORMExecutor($em, $purger);
		$executor->execute($fixtures);
	}
}
