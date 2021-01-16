<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CleanStyles;

use App\Model\Media\Entity\Repository\ImageRepository;
use App\Service\CacheStorage;
use App\Service\ImageStyle\ImageStyle;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required] public ImageRepository $repository;

    #[Required] public CacheStorage\Storage $localCache;

    #[Required] public ImageStyle $imageStyle;

    public function __invoke(Command $command): void
    {
        $image = $this->repository->get($command->id);
        $path = $image->getInfo()->getPath();
        $this->localCache->clean($path);
        $this->imageStyle->clean($path);
    }

}
