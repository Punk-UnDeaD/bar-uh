<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

interface AggregateRoot
{
    /** @return array<object> */
    public function releaseEvents(): array;
}
