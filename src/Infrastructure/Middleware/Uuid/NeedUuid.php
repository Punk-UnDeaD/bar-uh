<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\Uuid;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class NeedUuid
{
    public function __construct(public int $version = 4)
    {
    }
}
