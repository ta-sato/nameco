<?php

namespace Nameco\User\SchedulerBundle\DataFixtures\ORM;

use Nameco\User\SchedulerBundle\Entity\User;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
	private $container;
	
	public function __construct(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
	
	public function load(ObjectManager $em)
	{
		$factory = $this->container->get('security.encoder_factory');
		
		for ($i = 1; $i < 6; $i++)
		{
			$user = new User();
			$user->setUsername("test" . $i);
			$encoder  = $factory->getEncoder($user);
			$hashedPassword = $encoder->encodePassword("testtest", $user->getSalt());
			$user->setPassword($hashedPassword);
			$user->setName("Test" . $i . " Test" . $i);
			$user->setKana("test" . $i . " test" . $i);
			$user->setEmail("test" . $i . "@test.com");
			$user->setEnabled(true);
			$em->persist($user);
			// user-1 to user-5
			$this->addReference('user-' . $i, $user);
		}
		
		$em->flush();
	}
	
	public function getOrder()
	{
		return 1;
	}
}
