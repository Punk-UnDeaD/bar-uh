<?php

declare(strict_types=1);

namespace App\Service\ImageStyle\StyleUploader;

class Message
{
    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
