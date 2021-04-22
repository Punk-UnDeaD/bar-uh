<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\AsyncWrapper;

use Symfony\Component\Messenger\Stamp\StampInterface;

class AsyncStamp implements StampInterface
{
    public function __construct(public int $timeout = 0)
    {
    }
}
