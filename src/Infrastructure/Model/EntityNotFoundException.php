<?php

declare(strict_types=1);

namespace App\Infrastructure\Model;

use DomainException;

class EntityNotFoundException extends DomainException
{
    /** @var int */
    protected $code = 404;
}
