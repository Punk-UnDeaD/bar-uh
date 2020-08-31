<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Field;

use Doctrine\ORM\Mapping as ORM;

trait DateTrait
{
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
