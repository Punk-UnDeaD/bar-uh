<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle\StyleUploader;

use App\Media\Service\CacheStorage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    private FilesystemOperator $styleStorage;

    public function __construct(
        private CacheStorage\Storage $cacheStorage,
        FilesystemOperator $imageStyleStorage
    ) {
        $this->styleStorage = $imageStyleStorage;
    }

    public function __invoke(Message $message): void
    {
        if ($this->cacheStorage->has($path = $message->path)) {
            $this->styleStorage->writeStream(
                $path,
                $this->cacheStorage->readStream($path)
            );
        }
    }
}
