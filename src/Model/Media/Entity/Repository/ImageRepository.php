<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Repository;

use App\Model\Media\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function get(string $id): Image
    {
        return $this->find($id) ?? throw EntityNotFoundException::fromClassNameAndIdentifier('Image', [$id]);
    }
}
