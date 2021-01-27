<?php

declare(strict_types=1);

namespace App\ReadModel\Media;

class ImageRow
{
    public object $imageInfo;

    /** @var array<string> */
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
        /** @var array{width: int, height: int, alt?: string} $decodedImageInfo */
        $decodedImageInfo = json_decode($imageInfo, true);
        /** @psalm-suppress InvalidArgument false-positive */
        $this->imageInfo = new ImageInfo(...$decodedImageInfo);
        /** @var array<string> $tags */
        $tags = json_decode($tags ?? '[]');
        $this->tags = $tags;
    }
}
