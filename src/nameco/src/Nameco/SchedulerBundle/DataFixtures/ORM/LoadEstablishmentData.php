<?php

namespace Nameco\SchedulerBundle\DataFixtures\ORM;

use Nameco\SchedulerBundle\Entity\Area;
use Nameco\SchedulerBundle\Entity\Establishment;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadEstablishmentData extends AbstractFixture implements OrderedFixtureInterface
{
	private $container;

	public function __construct(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $em)
	{
		$area0 = new Area();
		$area0->setName("Area0");

		$area1 = new Area();
		$area1->setName("Area1");

		$em->persist($area0);
		$em->persist($area1);

		// area-0 and area-1
		$this->addReference('area-0', $area0);
		$this->addReference('area-1', $area1);

		for ($i = 0; $i < 5; $i++)
		{
			$e = new Establishment();
			$e->setName("Establishment_" . $i);
			$e->setEnabled(true);
			$e->setArea($em->merge($this->getReference('area-' . ($i % 2))));
			$em->persist($e);

			// establishment-0 to establishment-4
			$this->addReference('establishment-' . $i, $e);
		}

		$em->flush();
	}

	public function getOrder()
	{
		return 2;
	}
}
