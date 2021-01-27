<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\ExifSetValue;

use App\Model\Media\Entity\Repository\ImageRepository;
use App\Service\ExifEditor;
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

        $this->exifEditor->setExifProperty($image->getInfo()->getPath(), $command->key, $command->value);
    }
}
