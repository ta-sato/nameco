<?php

namespace Nameco\User\SchedulerBundle\DataFixtures\ORM;

use Nameco\User\SchedulerBundle\Entity\Area;
use Nameco\User\SchedulerBundle\Entity\Establishment;

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
		$area1 = new Area();
		$area1->setName("Area1");

		$area2 = new Area();
		$area2->setName("Area2");
		
		$em->persist($area1);
		$em->persist($area2);
		
		// area-1 and area-2
		$this->addReference('area-1', $area1);
		$this->addReference('area-2', $area2);
		
		for ($i = 1; $i < 3; $i++)
		{
			for ($j = 1; $j < 6; $j++)
			{
				$e = new Establishment();
				$e->setName("Establishment_" . $i . "-" . $j);
				$e->setEnabled(true);
				$e->setArea($em->merge($this->getReference('area-' . $i)));
				$em->persist($e);
				
				// establishment-1-1 to establishment-1-5
				// establishment-2-1 to establishment-2-5
				$this->addReference('establishment-' . $i . '-' . $j, $e);
			}
		}
		
		$em->flush();
	}
	
	public function getOrder()
	{
		return 2;
	}
}
