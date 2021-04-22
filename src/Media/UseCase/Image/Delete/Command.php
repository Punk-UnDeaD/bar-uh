<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\Delete;

class Command
{
    public function __construct(public string $id)
    {
    }
}
