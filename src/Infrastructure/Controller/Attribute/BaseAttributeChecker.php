<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Attribute;

use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/** @template T of object */
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
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var array{object, string}|callable $controller */
        $controller = $event->getController();

        if (!is_array($controller) || !is_object($controller[0])) {
            return;
        }
        if ($attribute = $this->getAttribute($controller[0], $controller[1])) {
            $this->checkAttribute($event, $attribute);
        }
    }

    /**
     * @return ?T
     *
     * @throws \ReflectionException
     */
    private function getAttribute(object $controller, string $method): ?object
    {
        $controllerReflection = new ReflectionClass($controller);
        $attributeClass = $this->getAttributeClass();
        $attributes = $controllerReflection->getMethod($method)->getAttributes($attributeClass)
            ?: $controllerReflection->getAttributes($attributeClass);
        if (empty($attributes)) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /** @return class-string<T> */
    abstract protected function getAttributeClass(): string;

    /** @param T $attribute */
    abstract protected function checkAttribute(ControllerArgumentsEvent $event, object $attribute): void;
}
