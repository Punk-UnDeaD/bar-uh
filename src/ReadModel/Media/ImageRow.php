<?php

declare(strict_types=1);

namespace App\ReadModel\Media;

class ImageRow
{
    public object $imageInfo;

    /** @var list<string> */
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
        /** @var array{width: int, height: int, alt: ?string} $decodedImageInfo */
        $decodedImageInfo = json_decode($imageInfo, true);
        $this->imageInfo = new ImageInfo(...$decodedImageInfo);
        /** @var list<string> $tags */
        $tags = json_decode($tags ?? '[]', true);
        $this->tags = $tags;
    }
}
