<?php

declare(strict_types=1);

namespace App\Media\Entity\Field;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    #[ORM\Id, ORM\Column(type: 'guid')]
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
