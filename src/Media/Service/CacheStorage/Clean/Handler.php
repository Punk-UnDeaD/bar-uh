<?php

declare(strict_types=1);

namespace App\Media\Service\CacheStorage\Clean;

use App\Media\Service\CacheStorage\Storage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required]
    public Storage $storage;

    public function __invoke(Command $command): void
    {
        $this->storage->delete($command->path);
    }
}
