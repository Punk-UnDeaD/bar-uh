<?php

declare(strict_types=1);

namespace App\Service\ImageStyle;

use App\Service\CacheStorage;
use JetBrains\PhpStorm\Pure;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ImageStyle
{
    const PREFIX = 'https://bar-uh-dev.website.yandexcloud.net/assets/image/';

    const STYLE_PREFIX = 'https://bar-uh-dev.website.yandexcloud.net/assets/style/';

    private FilesystemInterface $styleStorage;

    private FilesystemInterface $mainStorage;

    private FilesystemInterface $localCache;

    public function __construct(
        FilesystemInterface $imageStyleStorage,
        FilesystemInterface $imageMainStorage,
        CacheStorage\Storage $cacheStorage,
        private MessageBusInterface $bus
    ) {
        $this->styleStorage = $imageStyleStorage;
        $this->mainStorage = $imageMainStorage;
        $this->localCache = $cacheStorage;
    }

    public function url(string $path): string
    {
        return self::PREFIX.$path;
    }

    public function styleUrl(string $path, string $style, ?string $ext = null): string
    {
        return self::STYLE_PREFIX.$this->stylePath($path, $style, $ext);
    }

    public function stylePath(string $path, string $style, ?string $ext = null): string
    {
        $path = pathinfo($path);
        $ext ??= $path['extension'];

        return "{$path['dirname']}/{$path['filename']}/$style.{$ext}";
    }

    public function styleFile(string $path, string $style, string $ext, ...$params): string
    {
        $style_path = match ($style) {
            'width' => $this->stylePath($path, (string)$params[0], $ext),
            default => $this->stylePath($path, $style, $ext),
        };

        if (!$this->localCache->has($style_path)) {
            $local = $this->getOriginal($path);
            $dimensions = getimagesize($local);

            if ($this->canSkip($path, $style, $ext)) {
                $this->localCache->putStream($style_path, $this->localCache->readStream($path));
            } else {
                $this->localCache->prepareDir(dirname($style_path));

                $command = [
                    'magick',
                    'convert',
                    match ($ext) {
                        'gif', 'webp' => $local,
                        default => $local.'[0]'
                    },
                    match ($ext) {
                        'jpg' => '-quality 85',
                        default => '-quality 95',
                    },
                    $this->{$style}($dimensions, ...$params),
                    '-strip',
                    $this->localCache->getRealPath($style_path),
                ];
                exec(implode(' ', $command), $out, $return);
            }

            $this->bus->dispatch(new Optimizer\Message($style_path));
            $this->bus->dispatch(new StyleUploader\Message($style_path));
        }

        return $this->localCache->getRealPath($style_path);
    }

    private function getOriginal(string $path): string
    {
        return $this->localCache->getLocalCopy($path, $this->mainStorage);
    }

    #[Pure] private function canSkip(string $path, string $style, string $ext): bool
    {
        return 'self' === $style && 'jpeg' !== $ext && $ext === pathinfo($path, PATHINFO_EXTENSION);
    }

    private function self(): string
    {
        return '';
    }

    private function pixel(array $dimensions): string
    {
        return $this->width($dimensions, 8);
    }

    private function width($dimensions, int $width): string
    {
        return '-resize '.(100 * $width / $dimensions[0]).'%';
    }
}
