<?php

declare(strict_types=1);

namespace App\Media\Repository;

use App\Infrastructure\Model\GenericRepository;
use App\Media\Entity\Video;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends GenericRepository<Video, string>
 */
class VideoRepository extends GenericRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }
}
