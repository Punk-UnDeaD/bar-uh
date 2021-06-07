<?php

declare(strict_types=1);

namespace App\Media\Service\CacheStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\HttpFoundation\File\File;

class Adapter extends Local implements AdapterInterface
{
    /** @var array<array-key, mixed> */
    protected static $permissions
        = [
            'file' => [
                'public'  => 0666,
                'private' => 0600,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ],
        ];

    public function moveUploaded(File $file, string $path): File
    {
        $dir = dirname($this->applyPathPrefix($path));
        $this->ensureDirectory($dir);
        $file = $file->move($dir, basename($path));
        $this->setVisibility($path, 'public');

        return $file;
    }

    public function prepareDir(string $dir): void
    {
        $this->ensureDirectory($this->applyPathPrefix($dir));
    }
}