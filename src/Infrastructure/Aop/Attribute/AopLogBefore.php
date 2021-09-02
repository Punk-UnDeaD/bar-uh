<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AopLogBefore extends AopLogMethod
{
}
