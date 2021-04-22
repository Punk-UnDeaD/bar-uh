<?php

declare(strict_types=1);

namespace App\Media\Service;

use App\Infrastructure\Service\Transliterator;
use League\Flysystem\FilesystemInterface;

class ExifEditor
{
    public const EDITED_PROPS
        = [
            'Artist',
            'Copyright',
            'ImageDescription',
            'UserComment',
            'OwnerName',
        ];

    private FilesystemInterface $mainStorage;

    public function __construct(
        FilesystemInterface $imageMainStorage,
        private CacheStorage\Storage $localCache,
        private Transliterator $transliterator
    ) {
        $this->mainStorage = $imageMainStorage;
    }

    public function cleanThumbnail(string $path): void
    {
        $this->cleanExif($path, 'exif:thumbnail:*');
    }

    public function cleanExif(string $path, string $tag = 'all'): void
    {
        $props = [$tag => ''];
        if ('all' === $tag) {
            $props += array_filter($this->getExif($path));
        }
        $this->setExifProperties($path, $props);
    }

    /**
     * @return array<string, string>
     */
    public function getExif(string $path): array
    {
        $file = $this->localCache->hasDraft($path) ? $this->getDraft($path) : $this->getOriginal($path);
        $command = ['exiftool', ...array_map(fn(string $prop) => '-'.$prop, self::EDITED_PROPS), '-j', $file];
        exec(implode(' ', $command), $output);

        /** @var array<array<string, string>> $output */
        $output = json_decode(implode($output), true);
        $props = $output[0];
        unset($props['SourceFile']);

        return array_merge(array_fill_keys(self::EDITED_PROPS, ''), $props);
    }

    private function getDraft(string $path): string
    {
        return $this->localCache->getDraft($path, $this->mainStorage);
    }

    private function getOriginal(string $path): string
    {
        return $this->localCache->getLocalCopy($path, $this->mainStorage);
    }

    /**
     * @param array<string, string> $properties
     */
    public function setExifProperties(string $path, array $properties): void
    {
        $params = array_map(
            function(string $k, string $v): string {
                if ($v) {
                    if ('UserComment' !== $k) {
                        $v = $this->transliterator->transliterate($v);
                    }
                    $v = '"'.addcslashes($v ?: '', '"').'"';
                }

                return "-{$k}={$v}";
            },
            array_keys($properties),
            $properties
        );
        $command = ['exiftool', ...$params, '-overwrite_original', $this->getDraft($path)];
        exec(implode(' ', $command));
    }

    public function setExifProperty(string $path, string $key, string $value): void
    {
        $this->setExifProperties($path, [$key => $value]);
    }
}
