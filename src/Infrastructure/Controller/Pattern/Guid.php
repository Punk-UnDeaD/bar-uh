<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Pattern;

class Guid
{
    public const PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
}
