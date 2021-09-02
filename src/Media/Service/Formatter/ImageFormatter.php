<?php

declare(strict_types=1);

namespace App\Media\Service\Formatter;

use App\Infrastructure\Aop\Attribute\Aop;
use App\Infrastructure\Aop\Attribute\AopCacheResult;
use App\Infrastructure\Aop\Attribute\AopLog;

//#[Aop]
//#[AopLog]
class ImageFormatter
{
    public const PREFIX = '/storage/';

    public const STYLE_PREFIX = '/assets/style/';

    public function uri(string $path): string
    {
        return str_replace('public://', self::PREFIX, $path);
    }

    #[AopCacheResult]
    public function style(string $path, string $style): string
    {
        $path = str_replace('public://', '', $path);
        $path = pathinfo($path);

        return self::STYLE_PREFIX."{$path['dirname']}/{$path['filename']}/$style.".($path['extension'] ?? '');
    }
}
