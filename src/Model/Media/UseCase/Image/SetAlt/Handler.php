<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\SetAlt;

use App\Model\Media\Entity\Image;
use App\Model\Media\Entity\Repository\ImageRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required]
    public ImageRepository $repository;

    public function __invoke(Command $command): Image
    {
        $image = $this->repository->get($command->image);

        return $image->setImageInfo($image->getImageInfo()->setAlt($command->alt));
    }
}
