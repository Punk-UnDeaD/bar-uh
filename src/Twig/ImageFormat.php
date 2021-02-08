<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Formatter\ImageFormatter;
use App\Service\ImageStyle\ImageStyle;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ImageFormat extends AbstractExtension
{

    #[Required]
    public ImageFormatter $formatter;

    #[Required]
    public ImageStyle $imageStyle;

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
