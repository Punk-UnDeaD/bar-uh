<?php

declare(strict_types=1);

namespace App\Model\Field;

use Doctrine\ORM\Mapping as ORM;

trait DataTrait
{
    /**
     * @ORM\Column(type="json", options={"default" : "{}", "jsonb": true})
     */
    protected array $data = [];

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        $this->data = array_merge(static::defaultData(), $this->data);
    }

    protected static function defaultData(): array
    {
        return [];
    }
}
