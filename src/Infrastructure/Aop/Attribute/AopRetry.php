<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AopRetry extends Aop
{
    public int $count;

    public function __construct(int $count = 3, public int $msleep = 1000)
    {
        $this->count = max(1, $count);
    }
}
