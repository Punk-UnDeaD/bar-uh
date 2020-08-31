<?php

declare(strict_types=1);

namespace App\Model;

interface GetOneRepositoryInterface
{
    public function get($id): object;
}
