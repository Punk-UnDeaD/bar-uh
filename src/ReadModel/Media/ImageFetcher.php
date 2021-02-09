<?php

declare(strict_types=1);

namespace App\ReadModel\Media;

use App\ReadModel\Paginator;
use App\ReadModel\Denormalizer;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Service\Attribute\Required;

class ImageFetcher
{
    #[Required]
    public Connection $connection;

    #[Required]
    public Denormalizer $denormalizer;

    /**
     * @return Paginator<ImageRow>
     * @psalm-return Paginator
     */
    public function all(
        Filter\Filter $filter,
        int $page,
        int $size,
        string $sort = '',
        string $direction = 'ASC'
    ): Paginator {
        if (!$sort) {
            $sort = 'date';
            $direction = 'DESC';
        }

        $qb = $this->connection->createQueryBuilder()
            ->select(
                [
                    'id',
                    'date',
                    'info_name',
                    'info_path',
                    'info_mime',
                    'info_size',
                    'image_info',
                    "data->'tags' as tags",
                ]
            )
            ->from('media_images')
            ->orderBy($sort, $direction);
        if ($filter->mime) {
            $qb->andWhere('info_mime = :mime');
            $qb->setParameter(':mime', $filter->mime);
        }
        if ($filter->tag) {
            $qb->andWhere("jsonb_exists(data->'tags', :tag)");
            $qb->setParameter(':tag', $filter->tag);
        }

        return (new Paginator($qb, $page, $size))->setCallback(
            /** @param array<string, mixed> $row */
            fn (array $row): ImageRow => $this->denormalizer->denormalize($row, ImageRow::class)
        );
    }
}
