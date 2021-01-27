<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\DraftCreate;

use App\Model\Media\Entity\Repository\ImageRepository;
use App\Service\CacheStorage\Storage;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required] public ImageRepository $repository;

    #[Required] public Storage $storage;

    #[Required] public FilesystemInterface $imageMainStorage;

    public function __invoke(Command $command): string
    {
        $image = $this->repository->get($command->id);

        return $this->storage->getDraft($image->getInfo()->getPath(), $this->imageMainStorage);
    }
}
