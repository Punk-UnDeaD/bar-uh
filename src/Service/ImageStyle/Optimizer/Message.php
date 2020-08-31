<?php

declare(strict_types=1);

namespace App\Service\ImageStyle\Optimizer;

final class Message
{
    public function __construct(public string $path)
    {
    }
}
