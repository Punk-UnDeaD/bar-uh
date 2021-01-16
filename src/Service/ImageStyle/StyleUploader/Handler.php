<?php

declare(strict_types=1);

namespace App\Service\ImageStyle\StyleUploader;

use App\Service\CacheStorage;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{

    private FilesystemInterface $styleStorage;

    public function __construct(
        private CacheStorage\Storage $cacheStorage,
        FilesystemInterface $imageStyleStorage
    ) {
        $this->styleStorage = $imageStyleStorage;
    }

    public function __invoke(Message $message): void
    {
        if ($this->cacheStorage->has($path = $message->path)) {
            $this->styleStorage->putStream(
                $path,
                $this->cacheStorage->readStream($path)
            );
        }
    }
}
