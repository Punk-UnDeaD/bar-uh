<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\CreateFromUploaded;

class Command
{
    public string $name;

    public ?string $uuid;

    public function __construct(public string $path, string $name)
    {
        $this->name = pathinfo($name, PATHINFO_FILENAME);
    }
}
