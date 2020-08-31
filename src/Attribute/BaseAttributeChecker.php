<?php

declare(strict_types=1);

namespace App\Attribute;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class BaseAttributeChecker implements EventSubscriberInterface
{
    const ATTRIBUTE = '';

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

        $target = $event->getController();

        if (!is_array($target)) {
            return;
        }

        if ($attribute = $this->getAttribute(...$target)) {
            $this->checkAttribute($event, $attribute);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function getAttribute($controller, string $method): ?object
    {
        $reflection = new \ReflectionClass($controller);

        if ($attributes = $reflection->getMethod($method)->getAttributes(static::ATTRIBUTE)
            ?: $reflection->getAttributes(static::ATTRIBUTE)
        ) {
            return $attributes[0]->newInstance();
        }

        return null;
    }

    abstract protected function checkAttribute(ControllerArgumentsEvent $event, object $annotation);
}
