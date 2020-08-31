<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CreateFromUploaded;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Command
{

    public function __construct(public UploadedFile $file)
    {
    }
}
