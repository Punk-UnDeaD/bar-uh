<?php

declare(strict_types=1);

namespace App\Service\CacheStorage;

class Event
{
    public function __construct(public string $path)
    {
    }
}
