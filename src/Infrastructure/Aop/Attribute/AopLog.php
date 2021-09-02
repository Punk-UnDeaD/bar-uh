<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;
use Psr\Log\LogLevel;

#[Attribute(Attribute::TARGET_CLASS)]
class AopLog
{
    public function __construct(
        public string $prefix = '',
        public string $level = LogLevel::INFO,
        public string $channel = 'default',
    ) {
    }
}
