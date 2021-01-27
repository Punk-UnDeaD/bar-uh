<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\CreateFromUploaded;

use App\Model\Media\Entity\Image;
use App\Model\Media\UseCase\Image\BaseCreateHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

class Handler extends BaseCreateHandler implements MessageHandlerInterface
{
    public function __invoke(Command $command): Image
    {
        $uploadedFile = $command->file;
        $mimeType = $uploadedFile->getMimeType() ?: '';
        Assert::regex($mimeType, '/^image/', "{$uploadedFile->getClientOriginalName()} not image");
        $name = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME).
            '.'.($uploadedFile->guessExtension() ?? '');

        return $this->persist($uploadedFile, $name, $mimeType);
    }
}
