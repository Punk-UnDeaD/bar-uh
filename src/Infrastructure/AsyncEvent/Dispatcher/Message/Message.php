<?php

declare(strict_types=1);

namespace App\Infrastructure\AsyncEvent\Dispatcher\Message;

class Message
{
    private object $event;

    public function __construct(object $event)
    {
        $this->event = $event;
    }

    public function getEvent(): object
    {
        return $this->event;
    }
}
