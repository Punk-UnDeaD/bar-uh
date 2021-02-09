<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @template T of object
 * @template T1
 * @extends ServiceEntityRepository<T>
 */
abstract class GenericRepository extends ServiceEntityRepository
{
    /**
     * @psalm-param T1 $id
     *
     * @return T
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function get($id)
    {
        return $this->find($id) ??
            throw EntityNotFoundException::fromClassNameAndIdentifier($this->getClassName(), [(string)$id]);
    }
}
