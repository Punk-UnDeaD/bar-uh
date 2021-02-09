<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Repository;

use App\Model\GenericRepository;
use App\Model\Media\Entity\Image;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends GenericRepository<Image, string>
 */
class ImageRepository extends GenericRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }
}
