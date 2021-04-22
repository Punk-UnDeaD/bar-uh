<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\CreateFromUploaded;

use App\Media\Entity\Image;
use App\Media\UseCase\Image\BaseCreateHandler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler extends BaseCreateHandler implements MessageHandlerInterface
{
    public function __invoke(Command $command): Image
    {
        return $this->persist(new File($command->path), $command->name);
    }
}
