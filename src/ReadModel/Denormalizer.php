<?php

declare(strict_types=1);

namespace App\ReadModel;

class Denormalizer
{
    /**
     * @param array<string, mixed> $row
     *
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function denormalize(array $row, string $class): object
    {
        $row = array_combine(
            array_map([$this, 'toCamel'], array_keys($row)),
            array_values($row)
        );

        return new $class(...$row);
    }

    private function toCamel(string $snake): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $snake))));
    }
}
