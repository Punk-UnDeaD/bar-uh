<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\ExifSetValue;

use App\Infrastructure\Middleware\AsyncWrapper\Async;

#[Async]
class Command
{
    public function __construct(public string $id, public string $key, public string $value)
    {
    }
}
