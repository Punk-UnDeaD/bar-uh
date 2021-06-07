<?php

declare(strict_types=1);

namespace App\Media\Service\Formatter;

class ImageFormatter
{
    public const PREFIX = '/storage/';

    public const STYLE_PREFIX = '/assets/style/';

    public function uri(string $path): string
    {
        return str_replace('public://', self::PREFIX, $path);
    }

    public function style(string $path, string $style): string
    {
        $path = str_replace('public://', '', $path);
        $path = pathinfo($path);

        return self::STYLE_PREFIX."{$path['dirname']}/{$path['filename']}/$style.".($path['extension'] ?? '');
    }
}