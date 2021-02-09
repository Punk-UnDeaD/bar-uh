<?php

declare(strict_types=1);

namespace App\Model\Field;

trait TagTrait
{
    use DataTrait;

    /** @return list<string> */
    public function getTags(): array
    {
        /** @var list<string> $tags */
        $tags = $this->data['tags'] ?? [];

        return $tags;
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
