<?php

declare(strict_types=1);

namespace App\ReadModel\Media;

class ImageInfo
{
    public function __construct(public int $width, public int $height, public ?string $alt = null)
    {
    }
}
