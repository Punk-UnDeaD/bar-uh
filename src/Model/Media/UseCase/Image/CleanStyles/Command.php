<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CleanStyles;

class Command
{
    public function __construct(public string $id)
    {
    }
}
