<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AopLogMethod extends Aop
{
    public function __construct(
        public string $message,
        public ?string $level = null,
    ) {
    }
}
