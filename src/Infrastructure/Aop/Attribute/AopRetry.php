<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AopRetry
{
    public int $count;

    public function __construct(int $count = 3, public int $usleep = 1000)
    {
        $this->count = min(1, $count);
    }
}
