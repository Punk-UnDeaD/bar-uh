<?php

declare(strict_types=1);

namespace App\Service\ImageStyle\Optimizer;

use App\Service\CacheStorage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{

    public function __construct(private CacheStorage\Storage $storage)
    {
    }

    public function __invoke(Message $message)
    {
        if (!$this->storage->has($message->path)) {
            return;
        }
        $path = $this->storage->getRealPath($message->path);
        switch (true) {
            case 'png' === pathinfo($path, \PATHINFO_EXTENSION):
                exec("pngquant {$path} -f --strip -o {$path}");
                break;
        }
    }
}
