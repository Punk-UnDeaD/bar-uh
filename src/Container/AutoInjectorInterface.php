<?php

declare(strict_types=1);

namespace App\Container;

interface AutoInjectorInterface
{
    public function inject(string $field, $service);
}
