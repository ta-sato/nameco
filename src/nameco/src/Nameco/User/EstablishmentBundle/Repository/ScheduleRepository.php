<?php
namespace Nameco\User\EstablishmentBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{

	public function findAllByEstablishmentId($id)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT p FROM AcmeStoreBundle:Product p ORDER BY p.name ASC')
            ->getResult();
	}
}
