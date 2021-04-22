<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\AsyncWrapper;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class FallbackHandler implements MessageHandlerInterface
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function __invoke(Message $message): void
    {
        $this->bus->dispatch($message->message, [new SkipWrappingStamp()]);
    }
}
