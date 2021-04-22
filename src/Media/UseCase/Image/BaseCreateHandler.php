<?php

namespace App\Media\UseCase\Image;

use App\Infrastructure\Entity\Guid;
use App\Infrastructure\Flusher;
use App\Media\Entity\DataType\FileInfo;
use App\Media\Entity\DataType\ImageInfo;
use App\Media\Entity\Image;
use App\Media\Service\ImageStyle\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Service\Attribute\Required;
use Webmozart\Assert\Assert;

abstract class BaseCreateHandler
{
    #[Required] public EntityManagerInterface $em;

    #[Required] public Flusher $flusher;

    #[Required] public Uploader $uploader;

    protected function persist(File $file, string $name): Image
    {
        /** @var string $realPath */
        $realPath = $file->getRealPath();
        $mimeType = $file->getMimeType() ?? '';
        Assert::regex($mimeType, '/^image/', 'Not image');
        /** @var array<int> $dimensions */
        $dimensions = getimagesize($realPath);
        $size = $file->getSize();
        $id = Guid::next();
        $ext = $file->guessExtension();
        $name .= ".$ext";
        $this->em->persist(
            $image = new Image(
                $id,
                new FileInfo(
                    $this->uploader->upload($id, $file),
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
