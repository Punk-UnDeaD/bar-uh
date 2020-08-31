<?php

declare(strict_types=1);

namespace App\Service\ImageStyle;

use App\Service\CacheStorage;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;

class Uploader
{
    public function __construct(
        private FilesystemInterface $imageMainStorage,
        private CacheStorage\Storage $cacheStorage
    ) {
    }

    public function saveUploaded(string $id, File $file): string
    {
        $ext = $file->guessExtension();
        $path = date('Y/m/d').'/'.$id.($ext ? '.'.$ext : '');
        $file = $this->cacheStorage->moveUploaded($file, $path);
        $stream = fopen($file->getRealPath(), 'rb+');
        $this->imageMainStorage->writeStream($path, $stream);
        fclose($stream);

        return $path;
    }
}
