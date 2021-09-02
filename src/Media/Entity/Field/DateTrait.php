<?php

declare(strict_types=1);

namespace App\Media\Entity\Field;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait DateTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected DateTimeImmutable $date;

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
