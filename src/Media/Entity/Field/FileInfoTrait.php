<?php

declare(strict_types=1);

namespace App\Media\Entity\Field;

use App\Media\Entity\DataType\FileInfo;
use Doctrine\ORM\Mapping as ORM;

trait FileInfoTrait
{
    #[ORM\Embedded(class: FileInfo::class)]
    private FileInfo $info;

    public function getInfo(): FileInfo
    {
        return $this->info;
    }

    public function setInfo(FileInfo $info): void
    {
        $this->info = $info;
    }
}
