<?php

declare(strict_types=1);

namespace App\ReadModel\Media;

use App\Model\Media\Entity\DataType\ImageInfo;
use App\ReadModel\FromAssocInterface;
use App\ReadModel\FromAssocTrait;

class ImageRow
{
    public ImageInfo $imageInfo;

    public array $tags;

    public function __construct(
        public string $id,
        public string $date,
        public string $infoName,
        public string $infoPath,
        public string $infoMime,
        public int $infoSize,
        string $imageInfo,
        ?string $tags,
    ) {
        $imageInfo = json_decode($imageInfo, true);
        $this->imageInfo = new ImageInfo(...$imageInfo);
        $this->tags = json_decode($tags ?? '[]');
    }

}
