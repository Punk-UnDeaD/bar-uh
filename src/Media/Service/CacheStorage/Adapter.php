<?php

declare(strict_types=1);

namespace App\Media\Service\CacheStorage;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Symfony\Component\HttpFoundation\File\File;

class Adapter extends LocalFilesystemAdapter implements FilesystemAdapter
{
//    /** @var array */
//    protected static $permissions
//        = [
//            'file' => [
//                'public'  => 0666,
//                'private' => 0600,
//            ],
//            'dir'  => [
//                'public'  => 0777,
//                'private' => 0700,
//            ],
//        ];

    private PathPrefixer $prefixer;

    public function __construct(
        string $location,
        VisibilityConverter $visibility = null,
        int $writeFlags = LOCK_EX,
        int $linkHandling = self::DISALLOW_LINKS,
        MimeTypeDetector $mimeTypeDetector = null
    ) {
        $this->prefixer = new PathPrefixer($location, DIRECTORY_SEPARATOR);

        parent::__construct($location, $visibility, $writeFlags, $linkHandling, $mimeTypeDetector);
    }

    public function moveUploaded(File $file, string $path): File
    {
        $dir = dirname($this->applyPathPrefix($path));
        $this->ensureDirectoryExists($this->applyPathPrefix($dir), 0777);
        $file = $file->move($dir, basename($path));
        $this->setVisibility($path, 'public');

        return $file;
    }

    public function prepareDir(string $dir): void
    {
        $this->ensureDirectoryExists($this->applyPathPrefix($dir), 0777);
    }

    public function applyPathPrefix(string $path): string
    {
        return $this->prefixer->prefixPath($path);
    }
}
