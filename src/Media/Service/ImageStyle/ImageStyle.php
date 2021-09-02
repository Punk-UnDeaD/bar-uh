<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle;

use App\Infrastructure\Aop\Attribute\Aop;
use App\Infrastructure\Aop\Attribute\AopLog;
use App\Infrastructure\Aop\Attribute\AopLogBefore;
use App\Media\Service\CacheStorage;
use JetBrains\PhpStorm\Pure;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Process\Process;

#[Aop]
//#[AopLog('ImageStyle::')]
class ImageStyle
{
    public const PREFIX = 'https://bar-uh-dev.website.yandexcloud.net/assets/image/';

    public const STYLE_PREFIX = 'https://bar-uh-dev.website.yandexcloud.net/assets/style/';

    private FilesystemInterface $styleStorage;

    private FilesystemInterface $mainStorage;

    private CacheStorage\Storage $localCache;

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

    #[Pure]
    #[AopLogBefore('url {path}')]
    public function url(string $path): string
    {
        return self::PREFIX.$path;
    }

    public function styleUrl(string $path, string $style, ?string $ext = null): string
    {
        return self::STYLE_PREFIX.$this->stylePath($path, $style, $ext);
    }

    #[AopLogBefore('stylePath {path} {style} {ext}')]
    public function stylePath(string $path, string $style, ?string $ext = null): string
    {
        /** @var array{dirname: string, filename:string, extension: string} $pathInfo */
        $pathInfo = pathinfo($path);
        $ext ??= $pathInfo['extension'];

        return "{$pathInfo['dirname']}/{$pathInfo['filename']}/$style.{$ext}";
    }

    #[AopLogBefore('styleFile {path} {style} {ext}')]
    public function styleFile(string $path, string $style, string $ext, string|int ...$params): string
    {
        $style_path = match ($style) {
            'width' => $this->stylePath($path, (string)$params[0], $ext),
            default => $this->stylePath($path, $style, $ext),
        };

        if (!$this->localCache->has($style_path)) {
            $local = $this->getOriginal($path);
            $dimensions = getimagesize($local);

            if ($this->canSkip($path, $style, $ext)) {
                $this->localCache->putStream(
                    $style_path,
                    $this->localCache->readStream($path)
                );
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
                        'jpg'   => '-quality 85',
                        default => '-quality 95',
                    },
                    $this->{$style}($dimensions, ...$params),
                    '-strip',
                    $this->localCache->touch($style_path),
                ];
                Process::fromShellCommandline(implode(' ', $command))->run();
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

    #[Pure]
    private function canSkip(string $path, string $style, string $ext): bool
    {
        return 'self' === $style && 'jpeg' !== $ext && $ext === pathinfo($path, PATHINFO_EXTENSION);
    }

    public function clean(string $path): void
    {
        $path = pathinfo($path);
        $path = "{$path['dirname']}/{$path['filename']}";
        $this->localCache->deleteDir($path);
        $this->styleStorage->deleteDir($path);
    }

    private function self(): string
    {
        return '';
    }

    /**
     * @param list<int> $dimensions
     */
    private function pixel(array $dimensions): string
    {
        return $this->width($dimensions, 8);
    }

    /**
     * @param list<int> $dimensions
     */
    private function width(array $dimensions, int $width): string
    {
        return '-resize '.(100 * $width / $dimensions[0]).'%';
    }
}
