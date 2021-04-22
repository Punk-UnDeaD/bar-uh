<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\Delete;

use App\Media\Repository\ImageRepository;
use App\Media\Service\CacheStorage;
use App\Media\Service\ImageStyle\ImageStyle;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required]
    public ImageRepository $repository;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public CacheStorage\Storage $localCache;

    #[Required]
    public ImageStyle $imageStyle;

    #[Required]
    public FilesystemInterface $imageMainStorage;

    public function __invoke(Command $command): void
    {
        $image = $this->repository->get($command->id);
        $path = $image->getInfo()->getPath();
        $this->imageMainStorage->delete($path);
        $this->localCache->clean($path);
        $this->imageStyle->clean($path);
        $this->em->remove($image);
    }
}
