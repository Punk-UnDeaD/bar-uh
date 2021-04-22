<?php

declare(strict_types=1);

namespace App\Infrastructure\Entity;

use Symfony\Component\Uid\Ulid;

class Guid
{
    public static function next(): string
    {
        return (new Ulid())->toRfc4122();
    }
}
