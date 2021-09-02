<?php

declare(strict_types=1);

namespace App\Media\UseCase\Video\Create;

use DateTimeImmutable;

class Command
{
    public function __construct(public string $title, public string $uri, public ?DateTimeImmutable $date = null)
    {
    }
}
