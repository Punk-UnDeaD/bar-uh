<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

abstract class AopLogMethod extends Aop implements AopLog
{
    public function __construct(
        public string $message,
        public ?string $level = null,
    ) {
    }
}
