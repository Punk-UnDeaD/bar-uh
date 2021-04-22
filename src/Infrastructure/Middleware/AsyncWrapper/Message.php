<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\AsyncWrapper;

class Message
{
    public function __construct(public object $message)
    {
    }
}
