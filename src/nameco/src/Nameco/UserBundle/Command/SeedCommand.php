<?php

namespace Nameco\UserBundle\Command;

use Nameco\UserBundle\Entity\User;
use Nameco\UserBundle\Entity\Role;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SeedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('db:seed')
            ->setDescription('set default user value into db')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$e = $this->getContainer()->get('doctrine')->getEntityManager('default');

		$roles = array();
		$rolevalues = array(
			'ROLE_ADMIN' => '管理者',
			'ROLE_USER' => '一般',
			);
		foreach ($rolevalues as $key => $value) {
			$role = new Role();
			$role->setRole($key);
			$role->setName($value);
			$e->persist($role);
			$roles[] = $role;
		}
		$e->flush();
		
		$o = new User();
		$o->setFamilyName("admin");
		$o->setFirstName("nameco");
		$o->setKanaFamily("admin");
		$o->setKanaFirst("nameco");
		$o->setEmail("admin@example.com");
		$o->encodePassword($this->getContainer(), "namecoadmin");
		$o->addRole($roles[0]);
		$e->persist($o);
		$e->flush();

		$output->writeln("ok");
    }
}
