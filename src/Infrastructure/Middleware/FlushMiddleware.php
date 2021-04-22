<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class FlushMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);

        //        $result = $stack->next()->handle($envelope, $stack);
        //        $handledStamp = $result->last(HandledStamp::class);
        //        $res = $handledStamp?->getResult();
        //
        //        return $result;
    }
}
