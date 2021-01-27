<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\DataType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ImageInfoType extends JsonType
{
    public const NAME = 'media_image_info';

    /**
     * @param string $value
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @return ImageInfo
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var array{height: int, width: int, alt?: ?string} $value */
        $value = parent::convertToPHPValue($value, $platform);

        /** @psalm-suppress InvalidArgument false-positive */
        return new ImageInfo(...$value);
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
