<?php

declare(strict_types=1);

namespace App\Model\Field;

trait TagTrait
{
    use DataTrait;

    /**
     * @psalm-suppress MixedInferredReturnType
     *
     * @return list<string>
     */
    public function getTags(): array
    {
        return $this->data['tags'] ?? [];
    }

    /**
     * @param ?list<string> $tags
     */
    public function setTags(?array $tags = null): self
    {
        if ($tags) {
            $this->data['tags'] = $tags;
        } else {
            unset($this->data['tags']);
        }

        return $this;
    }
}
