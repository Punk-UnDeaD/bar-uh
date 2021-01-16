<?php

declare(strict_types=1);

namespace App\Model;

interface EventDispatcher
{
    /**
     * @param array<object> $events
     */
    public function dispatch(array $events): void;
}
