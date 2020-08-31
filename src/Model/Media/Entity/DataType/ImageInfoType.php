<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\DataType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ImageInfoType extends JsonType
{
    public const NAME = 'media_image_info';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        return !empty($value) ? new ImageInfo($value['width'], $value['height'], $value['alt'] ?? null) : null;
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
