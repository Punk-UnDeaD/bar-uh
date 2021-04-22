<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_size', [$this, 'fileSize'], ['is_safe' => ['html']]),
        ];
    }

    public function fileSize(int $size, int $precision = 2, string $space = ' '): string
    {
        if ($size <= 0) {
            return '0'.$space.'KB';
        }

        if (1 === $size) {
            return '1'.$space.'byte';
        }

        $mod = 1024;
        $units = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $size > $mod && $i < count($units) - 1; ++$i) {
            $size /= $mod;
        }

        return round($size, $precision).$space.$units[$i];
    }
}
