<?php

declare(strict_types=1);

namespace App\Service\CacheStorage;

use League\Flysystem\Adapter\Local;
use Symfony\Component\HttpFoundation\File\File;

class Adapter extends Local
{

    /** @var array<array<int>> */
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
