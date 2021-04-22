<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\SetTags;

use App\Media\Entity\Image;
use App\Media\Repository\ImageRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required]
    public ImageRepository $repository;

    public function __invoke(Command $command): Image
    {
        $image = $this->repository->get($command->image);

        return $image->setTags($command->tags);
    }
}
