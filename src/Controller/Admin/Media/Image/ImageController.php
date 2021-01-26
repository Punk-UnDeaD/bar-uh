<?php

declare(strict_types=1);

namespace App\Controller\Admin\Media\Image;

use App\Attribute\Guid;
use App\Attribute\RequiresCsrf;
use App\Model\Media\Entity\Image;
use App\Model\Media\UseCase\Image\CreateFromUploaded;
use App\Model\Media\UseCase\Image\CreateFromUrl;
use App\Model\Media\UseCase\Image\ExifClean;
use App\Model\Media\UseCase\Image\ExifSetValue;
use App\Model\Media\UseCase\Image\Delete;
use App\Model\Media\UseCase\Image\SetAlt;
use App\Model\Media\UseCase\Image\SetTags;
use App\Model\Media\UseCase\Image\CleanStyles;
use App\ReadModel\Media\Filter;
use App\ReadModel\Media\ImageFetcher;
use App\Service\CacheStorage\Storage;
use App\Service\ExifEditor;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/media/image', name: 'admin.media.image')]
class ImageController extends AbstractController
{
    private const PER_PAGE = 30;

    #[Route(path: '', name: '')]
    public function index(
        Request $request,
        ImageFetcher $fetcher
    ): Response {
        $filter = new Filter\Filter();
        $form = $this->createForm(Filter\Form::class, $filter);
        $form->handleRequest($request);
        $images = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('order_by', ''),
            $request->query->getAlpha('direction', 'ASC')
        );

        return $this->render(
            'admin/media/image/index.html.twig',
            [
                'images' => $images,
                'form'   => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{image}', name: '.show', requirements: ['image' => Guid::PATTERN], methods: ['GET'])]
    public function show(
        Image $image,
        Storage $storage
    ): Response {
        return $this->render(
            'admin/media/image/show.html.twig',
            [
                'image'        => $image,
                'localStorage' => $storage,
            ]
        );
    }

    #[Route(path: '/{id}', name: '.delete', requirements: ['image' => Guid::PATTERN], methods: ['DELETE'])]
    #[RequiresCsrf()]
    public function delete(
        Delete\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok']);
    }

    #[Route(path: '/upload', name: '.upload', methods: ['POST'])]
    public function upload(
        Request $request,
    ): JsonResponse {
        /** @var UploadedFile|mixed $file */
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                $this->dispatchMessage(new CreateFromUploaded\Command($file));
                $this->addFlash('success', "File {$file->getClientOriginalName()} saved.");
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
            }
        } elseif ($url = $request->request->get('url')) {
            $this->dispatchMessage(new CreateFromUrl\Command($url));
            $this->addFlash('success', "File {$url} saved.");
        }

        return $this->json(['status' => 'ok']);
    }

    #[Route(path: '/upload', name: '.uploadPage', methods: ['GET'])]
    public function uploadPage(): Response
    {
        return $this->render('admin/media/image/upload.html.twig');
    }

    #[Route(path: '/{image}/setTags', name: '.setTags', format: 'json')]
    #[RequiresCsrf]
    public function setTags(
        SetTags\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'value' => join(', ', $command->tags)]);
    }

    #[Route(path: '/{image}/setAlt', name: '.setAlt', format: 'json')]
    #[RequiresCsrf()]
    public function setAlt(
        SetAlt\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'value' => $command->alt]);
    }

    #[Route(path: '/{id}/cleanStyles', name: '.cleanStyles', format: 'json')]
    #[RequiresCsrf()]
    public function cleanStyles(
        CleanStyles\Command $command
    ): JsonResponse {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok']);
    }


    #[Route(path: '/{image}/exif', name: '.exif')]
    public function exif(
        Image $image,
        Storage $storage,
        ExifEditor $exifEditor
    ): Response {
        return $this->render(
            'admin/media/image/exif.html.twig',
            [
                'image'        => $image,
                'localStorage' => $storage,
                'exif'         => $exifEditor->getExif($image->getInfo()->getPath()),
            ]
        );
    }


    #[Route(path: '/{image}/draft', name: '.draft')]
    public function draft(
        Image $image,
        Storage $storage,
        FilesystemInterface $imageMainStorage
    ): Response {
        $draft = $storage->getDraft($image->getInfo()->getPath(), $imageMainStorage);

        return $this->render(
            'admin/media/image/draft.html.twig',
            [
                'image'        => $image,
                'draft'        => $draft,
                'localStorage' => $storage,
            ]
        );
    }

    #[Route(path: '/{image}/draft-delete', name: '.draft-delete', format: 'json')]
    #[RequiresCsrf()]
    public function draftDelete(
        Image $image,
        Storage $storage,
        FilesystemInterface $imageMainStorage
    ): Response {
        $draft = $storage->getDraft($image->getInfo()->getPath(), $imageMainStorage);

        return $this->render(
            'admin/media/image/draft.html.twig',
            [
                'image'        => $image,
                'draft'        => $draft,
                'localStorage' => $storage,
            ]
        );
    }

    #[Route(path: '/{id}/exif/clean', name: '.exif.clean', format: 'json')]
    #[RequiresCsrf()]
    public function exifClean(
        ExifClean\Command $command
    ): Response {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok', 'reload' => 1]);
    }

    #[Route(path: '/{id}/exif/setValue', name: '.exif.setValue', format: 'json')]
    #[RequiresCsrf()]
    public function exifSetValue(
        ExifSetValue\Command $command
    ): Response {
        $this->dispatchMessage($command);

        return $this->json(['status' => 'ok']);
    }
}
