<?php

declare(strict_types=1);

namespace App\ReadModel;

use DomainException;

class ReadModelNotFoundException extends DomainException
{
    protected $code = 404;
}
