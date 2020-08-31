<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CreateFromUrl;

class Command
{
    public function __construct(public string $url)
    {
    }
}
