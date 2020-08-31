<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Field;

use App\Model\Media\Entity\DataType\Id;
use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @ORM\Column(type="media_file_id")
     * @ORM\Id
     */
    private Id $id;

    public function getId(): Id
    {
        return $this->id;
    }
}
