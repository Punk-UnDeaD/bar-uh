<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

class OneTimeValidationStamp implements StampInterface
{
}
