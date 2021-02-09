<?php

declare(strict_types=1);

namespace App\Attribute;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/** @extends BaseAttributeChecker<RequiresCsrf> */
class RequiresCsrfSubscriber extends BaseAttributeChecker
{
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /** @param RequiresCsrf $attribute */
    protected function checkAttribute(ControllerArgumentsEvent $event, object $attribute): void
    {
        $request = $event->getRequest();
        /** @var ?string $token */
        $token = $request->headers->get('csrf-token') ?? $request->get('_csrf_token');
        /** @var string $tokenId */
        $tokenId = $attribute->tokenId ?? $request->get('_route');
        if ($this->csrfTokenManager->getToken($tokenId)->getValue() !== $token) {
            throw new AccessDeniedException('Invalid CSRF token.');
        }
    }

    protected function getAttributeClass(): string
    {
        return RequiresCsrf::class;
    }
}
