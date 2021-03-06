<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

trait EventsTrait
{
    private $recordedEvents = [];

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    protected function recordEvent(object ...$events): self
    {
        $this->recordedEvents = array_merge($this->recordedEvents, $events);

        return $this;
    }
}
