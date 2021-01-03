<?php

declare(strict_types=1);

namespace App\Model\Media\Entity\Field;

use App\Model\Media\Entity\DataType\FileInfo;
use Doctrine\ORM\Mapping as ORM;

trait FileInfoTrait
{
    /**
     * @ORM\Embedded(class="App\Model\Media\Entity\DataType\FileInfo")
     */
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
