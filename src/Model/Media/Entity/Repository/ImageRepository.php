<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Repository;

use App\Model\GetOneRepositoryInterface;
use App\Model\Media\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

class ImageRepository extends ServiceEntityRepository implements GetOneRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @param string $id
     */
    public function get($id): Image
    {
        /** @var ?Image $image */
        $image = $this->find($id);
        if (!$image) {
            throw EntityNotFoundException::fromClassNameAndIdentifier('Image', [$id]);
        }

        return $image;
    }
}
