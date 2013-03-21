<?php

namespace Nameco\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nameco\UserBundle\DependencyInjection\Compiler\MenuCompilerPass;

class NamecoUserBundle extends Bundle
{
	public function build(ContainerBuilder $container) {
		parent::build($container);
		
		$container->addCompilerPass(new MenuCompilerPass());
	}
}
