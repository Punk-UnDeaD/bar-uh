<?php

namespace App\Media\Entity;

use App\Infrastructure\Entity\Field\TagTrait;
use App\Media\Entity\DataType\FileInfo;
use App\Media\Entity\DataType\ImageInfo;
use App\Media\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'media_images')]
#[ORM\Index(columns: ['date'])]
#[ORM\Index(columns: ['info_name'])]
#[ORM\Index(columns: ['info_mime'])]
#[ORM\Index(columns: ['info_size'])]

class Image
{
    use Field\IdTrait;
    use Field\DateTrait;
    use Field\FileInfoTrait;
    use TagTrait;

    #[ORM\Column(type: 'media_image_info')]
    private ImageInfo $imageInfo;

    public function __construct(string $id, FileInfo $info, ImageInfo $imageInfo, ?DateTimeImmutable $date = null)
    {
        $this->id = $id;
        $this->info = $info;
        $this->imageInfo = $imageInfo;
        $this->date = $date ?? (new DateTimeImmutable());
    }

    public function getImageInfo(): ImageInfo
    {
        return $this->imageInfo;
    }

    public function setImageInfo(ImageInfo $imageInfo): Image
    {
        $this->imageInfo = $imageInfo;

        return $this;
    }
}
