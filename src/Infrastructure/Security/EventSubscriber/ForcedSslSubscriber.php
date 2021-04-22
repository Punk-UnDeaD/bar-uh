<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ForcedSslSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('Strict-Transport-Security', 'max-age=600');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Content-Security-Policy', 'style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com/css2');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
