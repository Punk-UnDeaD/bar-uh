<?php

declare(strict_types=1);

namespace App\Media\Entity\Field;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    #[ORM\Id, ORM\Column(type: 'guid')]
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
