<?php

declare(strict_types=1);

namespace App\Container;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AutoInjectorCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?? $name;
            if (in_array($class, get_declared_classes())
                && is_subclass_of($class, AutoInjectorInterface::class)
            ) {
                $reflection = new \ReflectionClass($class);
                $properties = $reflection->getProperties();
                foreach ($properties as $property) {
                    if ($attributes = $property->getAttributes(AutoInject::class)) {
                        $definition->addMethodCall(
                            'inject',
                            [$property->getName(), new Reference($property->getType()->getName())]
                        );
                    }
                }
            }
        }
    }
}
