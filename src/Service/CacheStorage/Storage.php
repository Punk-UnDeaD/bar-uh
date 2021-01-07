<?php

declare(strict_types=1);

namespace App\Service\CacheStorage;

use App\Event\Dispatcher\MessengerEventDispatcher;
use JetBrains\PhpStorm\ArrayShape;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class Storage extends Filesystem implements EventSubscriberInterface
{
    /**
     * @var Adapter
     */
    protected $adapter;

    private int $lifeTime;

    public function __construct(
        private string $cacheStorageFolder,
        private LockFactory $lockFactory,
        private MessengerEventDispatcher $dispatcher,
        int $cacheStorageLifeTime = 900
    ) {
        parent::__construct(new Adapter($cacheStorageFolder));
        $this->lifeTime = $cacheStorageLifeTime;
    }

    #[ArrayShape([Event::class => 'string'])]
    public static function getSubscribedEvents(): array
    {
        return [
            Event::class => 'deferredDelete',
        ];
    }

    public function moveUploaded(File $file, $path): File
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);

        return $this->adapter->moveUploaded($file, $path);
    }

    public function putStream($path, $resource, array $config = []): bool
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);

        return parent::putStream($path, $resource, $config); // TODO: Change the autogenerated stub
    }

    public function prepareDir($dir)
    {
        $this->adapter->prepareDir($dir);
    }

    public function put($path, $contents, array $config = []): bool
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);

        return parent::put($path, $contents, $config);
    }
    
    public function touch($path)
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);
        
        return $this->getRealPath($path);
    }
    
    public function write($path, $contents, array $config = []): bool
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);

        return parent::write($path, $contents, $config);
    }

    public function hasDraft($path): bool
    {
        return $this->has($path.'-draft');
    }

    public function deleteDraft($path): bool
    {
        return $this->delete($path.'-draft');
    }
    
    public function getDraft(string $path, FilesystemInterface $mainStorage): string
    {
        return $this->getLocalCopy($path, $mainStorage, true);
    }

    public function getLocalCopy(string $path, FilesystemInterface $mainStorage, $draft = false): string
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

    public function getLock($path): LockInterface
    {
        return $this->lockFactory->createLock(static::class.'/'.$path, null);
    }

    public function getRealPath(string $path): string
    {
        return $this->adapter->applyPathPrefix($path);
    }

    public function writeStream($path, $resource, array $config = []): bool
    {
        $this->dispatcher->dispatchDelay([new Event($path)], $this->lifeTime);

        return parent::write($path, $resource, $config); // TODO: Change the autogenerated stub
    }

    public function deferredDelete(Event $event): void
    {
        $this->getLock($event->path)->acquire(true);
        if ($this->has($event->path)) {
            $this->delete($event->path);
        }
    }

    public function clean($path)
    {
        $this->delete($path);
        $this->deleteDraft($path);
    }

    public function delete($path): bool
    {
        
        $deleted = $this->has($path) && parent::delete($path);
        if ($deleted) {
            while ('.' !== ($path = dirname($path))) {
                if (!$this->listContents($path)) {
                    $this->deleteDir($path);
                } else {
                    break;
                }
            }
        }

        return $deleted;
    }
}
