<?php

declare(strict_types=1);

namespace App\Media\ReadModel;

class ImageInfo
{
    public function __construct(public int $width, public int $height, public ?string $alt = null)
    {
    }
}
