<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\AsyncWrapper;

use ReflectionClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class WrapperMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if ($message instanceof Message) {
            $envelope = new Envelope($message->message, array_merge(...array_values($envelope->all())));
        } elseif (
            (null !== ($timeout = $this->getTimeout($envelope, $message)))
            && !$envelope->all(SkipWrappingStamp::class)
        ) {
            $envelope = new Envelope(new Message($message), array_merge(...array_values($envelope->all())));
            if ($timeout) {
                $envelope = $envelope->with(new DelayStamp($timeout * 1000));
            }
        }

        /** @psalm-suppress PossiblyInvalidArgument */
        return $stack->next()->handle($envelope, $stack);
    }

    private function getTimeout(Envelope $envelope, object $message): ?int
    {
        if ($stamp = $this->getAsyncStamp($envelope)) {
            return $stamp->timeout;
        }
        if ($attribute = $this->getAttribute($message)) {
            return $attribute->timeout;
        }

        return null;
    }

    private function getAsyncStamp(Envelope $envelope): ?AsyncStamp
    {
        if ($stamps = $envelope->all(AsyncStamp::class)) {
            return $stamps[0];
        }

        return null;
    }

    private function getAttribute(object $message): ?Async
    {
        if ($attributes = (new ReflectionClass($message))->getAttributes(
            Async::class
        )
        ) {
            /**
             * @var Async $attribute
             * @psalm-ignore-var
             */
            $attribute = $attributes[0]->newInstance();

            return $attribute;
        }

        return null;
    }
}
