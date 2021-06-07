<?php

declare(strict_types=1);

namespace App\Media\Entity\DataType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ImageInfoType extends JsonType
{
    public const NAME = 'media_image_info';

    public function convertToPHPValue($value, AbstractPlatform $platform): ImageInfo
    {
        /** @var array{height: int, width: int, alt: ?string} $phpValue */
        $phpValue = parent::convertToPHPValue($value, $platform);

        return new ImageInfo(...$phpValue);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
