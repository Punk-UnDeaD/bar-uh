<?php

declare(strict_types=1);

namespace App\Media\Controller\Admin\Image;

use App\Infrastructure\Controller\Attribute\RequiresCsrf;
use App\Media\UseCase\Image\DraftCreate;
use App\Media\UseCase\Image\DraftDelete;
use App\Media\UseCase\Image\ReplaceByDraft;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController, Route(path: '/admin/media/image/{id}/draft', name: 'admin.media.image.draft')]
class DraftController extends AbstractController
{
    #[Required] public MessageBusInterface $bus;

    #[Route(path: '', name: 'Create', methods: ['CREATE'])]
    #[RequiresCsrf]
    public function draftCreate(DraftCreate\Command $command): JsonResponse
    {
        $this->bus->dispatch($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }

    #[Route(path: '', name: 'Delete', methods: ['DELETE'])]
    #[RequiresCsrf]
    public function draftDelete(
        DraftDelete\Command $command
    ): JsonResponse {
        $this->bus->dispatch($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }

    #[Route(path: '/upload', name: 'Upload', methods: ['POST'])]
    #[RequiresCsrf]
    public function draftUpload(ReplaceByDraft\Command $command): JsonResponse
    {
        $this->bus->dispatch($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }
}
