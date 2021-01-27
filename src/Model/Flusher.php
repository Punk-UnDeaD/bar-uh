<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Flusher
{
    #[Required] public EntityManagerInterface $em;

    #[Required] public EventDispatcher $dispatcher;

    public function flush(AggregateRoot ...$roots): void
    {
        $this->em->flush();

        foreach ($roots as $root) {
            $this->dispatcher->dispatch($root->releaseEvents());
        }
    }
}
