<?php

declare(strict_types=1);

namespace App\Model;

interface GetOneRepositoryInterface
{
    /**
     * @param int|string $id
     */
    public function get($id): object;
}
