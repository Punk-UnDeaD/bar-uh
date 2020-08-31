<?php

declare(strict_types=1);

namespace App\Service;

use Transliterator as Translitera;

class Transliterator
{

    private ?Translitera $transliterator;

    private ?Translitera $transliteratorToASCII;

    public function __construct()
    {
        $this->transliterator = Translitera::create('Any-Latin');
        $this->transliteratorToASCII = Translitera::create('Latin-ASCII');
    }

    public function transliterate(string $s): string
    {
        return $this->transliteratorToASCII->transliterate($this->transliterator->transliterate($s));
    }
}
