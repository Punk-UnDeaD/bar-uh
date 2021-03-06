<?php

declare(strict_types=1);

namespace App\Media\Controller;

use App\Infrastructure\Controller\Pattern\Guid;
use App\Media\Entity\Image;
use App\Media\Service\ImageStyle\ImageStyle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[AsController, Route(
    path: '/assets/style/{date}/{image}',
    name: 'image.style',
    requirements: [
        'date'  => "\d{4}/\d{2}/\d{2}",
        'image' => Guid::PATTERN,
    ]
)]
class ImageStyleController extends AbstractController
{
    #[Required]
    public ImageStyle $imageStyle;

    #[Route(path: '/pixel.{ext}', name: '.pixel')]
    public function pixel(Image $image, string $ext): BinaryFileResponse
    {
        return new BinaryFileResponse($this->imageStyle->styleFile($image->getInfo()->getPath(), 'pixel', $ext));
    }

    #[Route(path: '/self.{ext}', name: '.self')]
    public function self(Image $image, string $ext): BinaryFileResponse
    {
        return new BinaryFileResponse($this->imageStyle->styleFile($image->getInfo()->getPath(), 'self', $ext));
    }

    #[Route(path: '/{width}.{ext}', name: '.width', requirements: ['width' => "\d+"])]
    public function width(Image $image, int $width, string $ext): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->imageStyle->styleFile($image->getInfo()->getPath(), 'width', $ext, $width)
        );
    }
}
