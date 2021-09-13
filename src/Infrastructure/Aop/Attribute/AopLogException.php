<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;
use Psr\Log\LogLevel;

#[Attribute(Attribute::TARGET_METHOD)]
class AopLogException extends Aop implements AopLog
{
    public function __construct(
        public string $message,
        public ?string $level = LogLevel::ALERT,
    ) {
    }
}
