<?php

declare(strict_types=1);

namespace App\Service;

class Denormalizer
{
    public function denormalize(array $row, string $class)
    {
        $row = array_merge(
            ...array_map(fn ($k, $v) => [$this->toCamel($k) => $v], array_keys($row), array_values($row))
        );

        return new $class(...$row);
    }

    private function toCamel(string $snake): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $snake))));
    }
}
