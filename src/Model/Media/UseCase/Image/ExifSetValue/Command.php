<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\ExifSetValue;

class Command
{
    public function __construct(public string $id, public string $key, public string $value)
    {
    }
}
