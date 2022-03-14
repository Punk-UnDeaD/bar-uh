<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle;

use App\Media\Service\CacheStorage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

class Uploader
{
    public function __construct(
        private FilesystemOperator $imageMainStorage,
        private CacheStorage\Storage $cacheStorage
    ) {
    }

    public function upload(string $id, File $file): string
    {
        $ext = $file->guessExtension();
        $path = date('Y/m/d').'/'.$id.($ext ? '.'.$ext : '');
        /** @var string $realPath */
        $realPath = $this->cacheStorage->moveUploaded($file, $path)->getRealPath();
        /** @var resource $stream */
        $stream = fopen($realPath, 'rb+');
        $this->imageMainStorage->writeStream($path, $stream);
        fclose($stream);

        return $path;
    }
}
