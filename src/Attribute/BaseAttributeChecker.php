<?php

declare(strict_types=1);

namespace App\Attribute;

use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class BaseAttributeChecker implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerArgumentsEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var array{object, string}|callable $target */
        $target = $event->getController();

        if (!is_array($target) || !is_object($target[0])) {
            return;
        }
        /** @psalm-suppress MixedArgument */
        if ($attribute = $this->getAttribute(...$target)) {
            $this->checkAttribute($event, $attribute);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function getAttribute(object $controller, string $method): ?object
    {
        $controllerReflection = new ReflectionClass($controller);
        $attributeClass = $this->getAttributeClass();
        if ($attributes = $controllerReflection->getMethod($method)->getAttributes($attributeClass)
            ?: $controllerReflection->getAttributes($attributeClass)
        ) {
            return $attributes[0]->newInstance();
        }

        return null;
    }

    /**
     * @return class-string
     */
    abstract protected function getAttributeClass(): string;

    abstract protected function checkAttribute(ControllerArgumentsEvent $event, object $annotation): void;
}
