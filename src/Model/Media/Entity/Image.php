<?php

namespace App\Model\Media\Entity;

use App\Model\Field\TagTrait;
use App\Model\Media\Entity\DataType\FileInfo;
use App\Model\Media\Entity\DataType\Id;
use App\Model\Media\Entity\DataType\ImageInfo;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Media\Entity\Repository\ImageRepository")
 * @ORM\Table(name="media_images", indexes={
 *     @ORM\Index(columns={"date"}),
 *     @ORM\Index(columns={"info_name"}),
 *     @ORM\Index(columns={"info_mime"}),
 *     @ORM\Index(columns={"info_size"})
 *     })
 */
class Image
{
    use Field\IdTrait;
    use Field\DateTrait;
    use Field\FileInfoTrait;

    use TagTrait;

    /**
     * @ORM\Column(type="media_image_info")
     */
    private ImageInfo $imageInfo;

    public function __construct(Id $id, FileInfo $info, ImageInfo $imageInfo, ?DateTimeImmutable $date = null)
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
