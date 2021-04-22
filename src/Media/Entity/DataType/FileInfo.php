<?php

declare(strict_types=1);

namespace App\Media\Entity\DataType;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class FileInfo
{
    #[ORM\Column(type: 'string')]
    private string $path;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $mime;

    #[ORM\Column(type: 'integer')]
    private int $size;

    public function __construct(string $path, string $name, string $mime, int $size)
    {
        $this->path = $path;
        $this->name = $name;
        $this->mime = $mime;
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $clone = clone $this;
        $clone->size = $size;

        return $clone;
    }
}
