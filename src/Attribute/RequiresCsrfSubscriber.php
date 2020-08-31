<?php

declare(strict_types=1);

namespace App\Attribute;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RequiresCsrfSubscriber extends BaseAttributeChecker
{
    public const ATTRIBUTE = RequiresCsrf::class;

    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param object|RequiresCsrf $annotation
     */
    protected function checkAttribute(ControllerArgumentsEvent $event, object $annotation)
    {
        $request = $event->getRequest();
        $token = $request->headers->get('csrf-token') ?? $request->get('_csrf_token');
        $tokenId = $annotation->tokenId ?? $request->get('_route');
        if ($this->csrfTokenManager->getToken($tokenId)->getValue() !== $token) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }
    }
}
