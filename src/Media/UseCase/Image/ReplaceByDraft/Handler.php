<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\ReplaceByDraft;

use App\Infrastructure\Middleware\AsyncWrapper\Async;
use App\Media\Repository\ImageRepository;
use App\Media\Service\CacheStorage\Storage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Async]
class Handler implements MessageHandlerInterface
{
    #[Required] public ImageRepository $repository;

    #[Required] public Storage $storage;

    #[Required] public FilesystemOperator $imageMainStorage;

    public function __invoke(Command $command): void
    {
        $image = $this->repository->get($command->id);
        $path = $image->getInfo()->getPath();
        $draft = $this->storage->getDraft($path, $this->imageMainStorage);
        $image->setInfo($image->getInfo()->setSize(filesize($draft) ?: 0));
        /** @var resource $stream */
        $stream = fopen($draft, 'rb+');
        $this->imageMainStorage->writeStream($path, $stream);
        $this->storage->deleteDraft($path);
        $this->storage->delete($path);
    }
}
