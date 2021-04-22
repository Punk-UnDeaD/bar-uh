<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\AsyncWrapper;

use Attribute;
use Symfony\Component\Messenger\Stamp\StampInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class Async implements StampInterface
{
    public function __construct(public int $timeout = 0)
    {
    }
}
