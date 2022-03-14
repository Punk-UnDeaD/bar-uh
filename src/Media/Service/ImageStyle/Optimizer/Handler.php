<?php

declare(strict_types=1);

namespace App\Media\Service\ImageStyle\Optimizer;

use App\Infrastructure\Aop\Attribute\Aop;
use App\Media\Service\CacheStorage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Process\Process;
use const PATHINFO_EXTENSION;

// #[Aop]
// #[AopLog('Optimizer')]
class Handler implements MessageHandlerInterface
{
    public function __construct(private CacheStorage\Storage $storage)
    {
    }

    public function __invoke(Message $message): void
    {
        if (!$this->storage->has($message->path)) {
            return;
        }
        $path = $this->storage->getRealPath($message->path);
        switch (true) {
            case 'png' === pathinfo($path, PATHINFO_EXTENSION):
                Process::fromShellCommandline("pngquant {$path} -f --strip -o {$path}")->run();
                break;
        }
    }
}
