<?php

declare(strict_types=1);

namespace App\Infrastructure\AsyncEvent\Dispatcher\Message;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Message $message): void
    {
        $this->dispatcher->dispatch($message->getEvent());
    }
}
