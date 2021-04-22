<?php

declare(strict_types=1);

namespace App\Media\Service\CacheStorage\Clean;

class Command
{
    public function __construct(public string $path)
    {
    }
}
