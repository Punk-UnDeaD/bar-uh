<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use App\Infrastructure\Aop\Attribute\Aop;
use App\Infrastructure\Aop\Attribute\AopCacheResult;
use App\Infrastructure\Aop\Attribute\AopLogClass;
use Symfony\Contracts\Service\ResetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[Aop]
#[AopLogClass(channel: 'media')]
class HoarderExtension extends AbstractExtension
    implements ResetInterface
{
    public \Psr\Log\LoggerInterface $mediaLogger;


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
        $this->mediaLogger->info('flushEntries');

        return $entries;
    }

    public function reset(): void
    {
        $this->mediaLogger->info('reset');

        $this->entries = [];
    }

    #[AopCacheResult]
    public function fakerCache(){
        return 1;
    }
}
