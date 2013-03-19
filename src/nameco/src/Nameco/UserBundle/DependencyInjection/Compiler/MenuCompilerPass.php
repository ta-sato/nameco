<?php

namespace Nameco\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MenuCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('nameco.menumanager')) {
            return;
        }
        $definition = $container->getDefinition('nameco.menumanager');

        $taggedServices = $container->findTaggedServiceIds('nameco.menuitem');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addItem',
                array(new Reference($id))
            );
        }
    }
}