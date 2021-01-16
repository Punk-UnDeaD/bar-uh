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
     * @param string $id
     */
    public function get($id): Image
    {
        if (!$image = $this->find($id)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier('Image', [$id]);
        }

        return $image;
    }
}
