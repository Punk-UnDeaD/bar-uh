<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\ReplaceByDraft;

use App\Model\Media\Entity\Repository\ImageRepository;
use App\Service\CacheStorage\Storage;
use App\Service\ExifEditor;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{

    #[Required] public ImageRepository $repository;

    #[Required] public Storage $storage;

    #[Required] public FilesystemInterface $imageMainStorage;

    public function __invoke(Command $command)
    {
        $image = $this->repository->get($command->id);
        $path = $image->getInfo()->getPath();
        $draft = $this->storage->getDraft($path, $this->imageMainStorage);
        $image->setInfo($image->getInfo()->setSize(filesize($draft)));
        $stream = fopen($draft, 'rb+');
        $this->imageMainStorage->updateStream($path, $stream);
        $this->storage->deleteDraft($path);
        $this->storage->delete($path);
    }

}
