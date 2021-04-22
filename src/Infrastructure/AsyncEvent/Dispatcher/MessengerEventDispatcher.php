<?php

declare(strict_types=1);

namespace App\Infrastructure\AsyncEvent\Dispatcher;

use App\Infrastructure\AsyncEvent\Dispatcher\Message\DebounceMessage;
use App\Infrastructure\AsyncEvent\Dispatcher\Message\Message;
use App\Infrastructure\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class MessengerEventDispatcher implements EventDispatcher
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @param array<object> $events
     */
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch(new Message($event));
        }
    }

    /**
     * @param array<object> $events
     */
    public function dispatchDelay(array $events, int $delay = 1): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch(
                new Message($event),
                [new DelayStamp($delay * 1000)]
            );
        }
    }

    public function dispatchDebounce(object $event, string $id, int $delay = 1, ?string $name = null): void
    {
        $this->bus->dispatch(
            new DebounceMessage($event, $id, $name),
            [new DelayStamp($delay * 1000)]
        );
    }
}
