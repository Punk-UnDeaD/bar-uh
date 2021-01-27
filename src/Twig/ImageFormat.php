<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Formatter\ImageFormatter;
use App\Service\ImageStyle\ImageStyle;
use JetBrains\PhpStorm\Pure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageFormat extends AbstractExtension
{
    public function __construct(private ImageFormatter $formatter, private ImageStyle $imageStyle)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('image_main_url', [$this, 'imageMainUrl'], ['is_safe' => ['html']]),
            new TwigFilter('image_style_url', [$this, 'imageStyleUrl'], ['is_safe' => ['html']]),
        ];
    }

    #[Pure]
 public function imageMainUrl(string $path): string
 {
     return $this->imageStyle->url($path);
 }

    public function imageStyleUrl(string $path, string $style, ?string $ext = null): string
    {
        return $this->imageStyle->styleUrl($path, $style, $ext);
    }
}
