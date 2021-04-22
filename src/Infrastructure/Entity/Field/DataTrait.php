<?php

declare(strict_types=1);

namespace App\Infrastructure\Entity\Field;

use Doctrine\ORM\Mapping as ORM;

trait DataTrait
{
    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json', options: ['default' => '{}', 'jsonb' => true])]
    protected array $data = [];

    #[ORM\PostLoad]
    public function checkData(): void
    {
        $this->data = array_merge(static::defaultData(), $this->data);
    }

    /** @return array<string, mixed> */
    protected static function defaultData(): array
    {
        return [];
    }
}
