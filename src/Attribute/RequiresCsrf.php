<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class RequiresCsrf
{
    public function __construct(public ?string $tokenId = null)
    {
    }
}
