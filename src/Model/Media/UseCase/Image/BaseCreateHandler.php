<?php

namespace App\Model\Media\UseCase\Image;

use App\Model\Flusher;
use App\Model\Media\Entity\DataType\FileInfo;
use App\Model\Media\Entity\DataType\Id;
use App\Model\Media\Entity\DataType\ImageInfo;
use App\Model\Media\Entity\Image;
use App\Service\ImageStyle\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseCreateHandler
{
    #[Required] public EntityManagerInterface $em;

    #[Required] public Flusher $flusher;

    #[Required] public Uploader $uploader;

    protected function persist(File $uploadedFile, string $name, string $mimeType): Image
    {
        /** @var string $realPath */
        $realPath = $uploadedFile->getRealPath();
        /** @var array<int> $dimensions */
        $dimensions = getimagesize($realPath);
        $size = $uploadedFile->getSize();
        $id = Id::next();
        $this->em->persist(
            $image = new Image(
                $id,
                new FileInfo(
                    $this->uploader->saveUploaded($id->getValue(), $uploadedFile),
                    $name,
                    $mimeType,
                    $size
                ),
                new ImageInfo($dimensions[0], $dimensions[1])
            )
        );

        return $image;
    }
}
