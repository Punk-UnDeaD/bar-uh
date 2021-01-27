<?php

declare(strict_types=1);

namespace App\Controller\Admin\Media\Image;

use App\Attribute\RequiresCsrf;
use App\Model\Media\UseCase\Image\DraftCreate;
use App\Model\Media\UseCase\Image\DraftDelete;
use App\Model\Media\UseCase\Image\ReplaceByDraft;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/media/image/{id}/draft', name: 'admin.media.image.draft')]
class DraftController extends AbstractController
{
    #[Route(path: '', name: 'Create', methods: ['CREATE'])]
    #[RequiresCsrf]
    public function draftCreate(
        DraftCreate\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }

    #[Route(path: '', name: 'Delete', methods: ['DELETE'])]
    #[RequiresCsrf]
    public function draftDelete(
        DraftDelete\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }

    #[Route(path: '/upload', name: 'Upload', methods: ['POST'])]
    #[RequiresCsrf]
    public function draftUpload(
        ReplaceByDraft\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'reload' => true]);
    }
}
