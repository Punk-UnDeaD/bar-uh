<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle\Optimizer;

use App\Infrastructure\Middleware\AsyncWrapper\Async;

#[Async(10)]
final class Message
{
    public function __construct(public string $path)
    {
    }
}
