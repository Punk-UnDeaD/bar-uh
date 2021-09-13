<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AopRetryLog extends AopRetry implements AopLog
{
    public int $count;

    public function __construct(
        public string $message,
        public ?string $level = null,
        int $count = 3,
        public int $msleep = 30
    ) {
        $this->count = max(1, $count);
    }
}
