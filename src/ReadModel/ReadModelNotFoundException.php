<?php

declare(strict_types=1);

namespace App\ReadModel;

use DomainException;

class ReadModelNotFoundException extends DomainException
{
    /** @var int */
    protected $code = 404;
}
