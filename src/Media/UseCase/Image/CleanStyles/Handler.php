<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\CleanStyles;

use App\Infrastructure\Middleware\AsyncWrapper\Async;
use App\Media\Repository\ImageRepository;
use App\Media\Service\CacheStorage;
use App\Media\Service\ImageStyle\ImageStyle;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Async]
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
