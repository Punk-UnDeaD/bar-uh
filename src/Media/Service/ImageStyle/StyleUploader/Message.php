<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle\StyleUploader;

use App\Infrastructure\Middleware\AsyncWrapper\Async;

#[Async(20)]
class Message
{
    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
}
