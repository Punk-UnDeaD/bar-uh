<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use Symfony\Contracts\Service\ResetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HoarderExtension extends AbstractExtension implements ResetInterface
{
    /** @var array<string> */
    private array $entries = [];

    public function addEntry(string $entry): void
    {
        $this->entries[] = $entry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('addEntry', [$this, 'addEntry']),
            new TwigFunction('flushEntries', [$this, 'flushEntries']),
        ];
    }

    /** @return array<string> */
    public function flushEntries(): array
    {
        $entries = array_unique($this->entries);
        $this->entries = [];

        return $entries;
    }

    public function reset(): void
    {
        $this->entries = [];
    }
}
