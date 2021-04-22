<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\ExifClean;

use App\Media\Repository\ImageRepository;
use App\Media\Service\ExifEditor;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required]
    public ExifEditor $exifEditor;

    #[Required]
    public ImageRepository $repository;

    public function __invoke(Command $command): void
    {
        $image = $this->repository->get($command->id);

        $this->exifEditor->cleanExif($image->getInfo()->getPath(), $command->tag);
    }
}
