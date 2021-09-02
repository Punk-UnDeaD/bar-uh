<?php

declare(strict_types=1);

namespace App\Media\Entity\DataType;

use JsonSerializable;

class ImageInfo implements JsonSerializable
{
    public function __construct(private int $width, private int $height, private ?string $alt = null)
    {
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        if ($this->alt === $alt) {
            return $this;
        }
        $clone = clone $this;
        $clone->alt = $alt;

        return $clone;
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return ['width' => $this->width, 'height' => $this->height] + ($this->alt ? ['alt' => $this->alt] : []);
    }
}
