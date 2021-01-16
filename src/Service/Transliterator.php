<?php

declare(strict_types=1);

namespace App\Service;

use JetBrains\PhpStorm\Pure;

class Transliterator
{

    /**
     * @var array<\Transliterator>
     */
    private array $transliterators;

    public function __construct(\Transliterator ...$transliterators)
    {
        $this->transliterators = $transliterators;
    }

    #[Pure] public function transliterate(string $s): string|false
    {
        foreach ($this->transliterators as $transliterator) {
            $s = $transliterator->transliterate($s);
            if (false === $s) {
                break;
            }
        }

        return $s;
    }
}
