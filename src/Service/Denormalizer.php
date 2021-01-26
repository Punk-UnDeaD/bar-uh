<?php

declare(strict_types=1);

namespace App\Service;

class Denormalizer
{

    /**
     * @param array<string, mixed> $row
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function denormalize(array $row, string $class): object
    {
        $row = array_merge(
            ...array_map(fn (string $k, mixed $v) => [$this->toCamel($k) => $v], array_keys($row), array_values($row))
        );

        /** @psalm-suppress MixedMethodCall */
        return new $class(...$row);
    }

    private function toCamel(string $snake): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $snake))));
    }
}
