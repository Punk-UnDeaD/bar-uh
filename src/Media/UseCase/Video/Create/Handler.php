<?php

declare(strict_types=1);

namespace App\Media\UseCase\Video\Create;

use App\Infrastructure\Entity\Guid;
use App\Media\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class Handler implements MessageHandlerInterface
{
    #[Required] public EntityManagerInterface $em;

    public function __invoke(Command $command): Video
    {
        $video = new Video(Guid::next(), $command->title, $command->uri, $command->date);
        $this->em->persist($video);

        return $video;
    }
}
