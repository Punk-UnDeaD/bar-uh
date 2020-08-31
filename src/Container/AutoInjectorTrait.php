<?php

declare(strict_types=1);

namespace App\Container;

trait AutoInjectorTrait
{
    public function inject(string $field, $service)
    {
        $this->$field = $service;
    }
}
