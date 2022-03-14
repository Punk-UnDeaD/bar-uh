<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\DraftCreate;

use App\Media\Repository\ImageRepository;
use App\Media\Service\CacheStorage\Storage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required] public ImageRepository $repository;

    #[Required] public Storage $storage;

    #[Required] public FilesystemOperator $imageMainStorage;

    public function __invoke(Command $command): string
    {
        $image = $this->repository->get($command->id);

        return $this->storage->getDraft($image->getInfo()->getPath(), $this->imageMainStorage);
    }
}
