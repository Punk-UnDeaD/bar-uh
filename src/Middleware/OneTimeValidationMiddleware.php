<?php

declare(strict_types=1);

namespace App\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\ValidationMiddleware;

class OneTimeValidationMiddleware extends ValidationMiddleware
{

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->all(OneTimeValidationStamp::class)) {
            return $stack->next()->handle($envelope, $stack);
        }

        return parent::handle($envelope->with(new OneTimeValidationStamp()), $stack);
    }
}
