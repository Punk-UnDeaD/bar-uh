<?php

declare(strict_types=1);

namespace App\Media\Service\CacheStorage;

use App\Infrastructure\Middleware\AsyncWrapper\AsyncStamp;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Autoconfigure(bind: [
    '$lockFactory'          => '@lockFactory',
    '$cacheStorageFolder'   => '%kernel.project_dir%/var/cache-storage',
    '$cacheStorageLifeTime' => '%env(CACHE_STORAGE_LIFE_TIME)%',
])]
class Storage extends Filesystem
{
    #[Required]
    public MessageBusInterface $bus;

    /**
     * @var \League\Flysystem\FilesystemAdapter
     */
    protected $adapter;

    /** @var Adapter */
    protected $adapterIpm;

    private int $lifeTime;

    public function __construct(
        string $cacheStorageFolder,
        private LockFactory $lockFactory,
        int $cacheStorageLifeTime = 30
    ) {
        $this->adapterIpm = new Adapter($cacheStorageFolder);
        parent::__construct($this->adapterIpm);
        $this->lifeTime = $cacheStorageLifeTime;
    }

    public function moveUploaded(File $file, string $path): File
    {
        $this->bus->dispatch(new Clean\Command($path), [new AsyncStamp($this->lifeTime)]);

        return $this->adapterIpm->moveUploaded($file, $path);
    }

    public function prepareDir(string $dir): void
    {
        $this->adapterIpm->prepareDir($dir);
    }

    /**
     * @param array<mixed> $config
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->touch($location);

        parent::write($location, $contents, $config);
    }

    public function touch(string $path): string
    {
        $this->bus->dispatch(new Clean\Command($path), [new AsyncStamp($this->lifeTime)]);

        return $this->getRealPath($path);
    }

    public function getRealPath(string $path): string
    {
        return $this->adapterIpm->applyPathPrefix($path);
    }

    public function hasDraft(string $path): bool
    {
        return $this->has($path.'-draft');
    }

    public function getDraft(string $path, FilesystemOperator $mainStorage): string
    {
        return $this->getLocalCopy($path, $mainStorage, true);
    }

    public function getLocalCopy(string $path, FilesystemOperator $mainStorage, bool $draft = false): string
    {
        $lock = $this->getLock($path);
        $lock->acquire(true);

        if ($this->has($path.($draft ? '-draft' : ''))) {
            return $this->getRealPath($path.($draft ? '-draft' : ''));
        }
        if (!$this->has($path)) {
            $stream = $mainStorage->readStream($path);
            $this->writeStream($path, $stream);
        }
        if ($draft) {
            if (!$this->has($path.'-draft')) {
                $this->copy($path, $path.'-draft');
            }
        }

        return $this->getRealPath($path.($draft ? '-draft' : ''));
    }

    public function getLock(string $path): LockInterface
    {
        return $this->lockFactory->createLock(static::class.'/'.$path, null);
    }

    /**
     * @param resource     $contents
     * @param array<mixed> $config
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->touch($location);
        parent::writeStream($location, $contents, $config);
    }

    public function clean(string $location): void
    {
        $this->delete($location);
        $this->deleteDraft($location);
    }

    public function delete(string $location): void
    {
        $this->getLock($location)->acquire(true);

        $deleted = $this->has($location);
        parent::delete($location);
        if ($deleted) {
            while ('.' !== ($location = dirname($location))) {
                if (!$this->listContents($location)->toArray()) {
                    parent::delete($location);
                } else {
                    break;
                }
            }
        }
    }

    public function deleteDraft(string $location): void
    {
        $this->delete($location.'-draft');
    }
}
