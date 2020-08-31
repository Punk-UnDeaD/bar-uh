<?php

declare(strict_types=1);

namespace App\Model\Field;

trait TagTrait
{
    use DataTrait;

    public function getTags(): array
    {
        return $this->data['tags'] ?? [];
    }

    public function setTags(array $tags = []): self
    {
        if ($tags) {
            $this->data['tags'] = $tags;
        } else {
            unset($this->data['tags']);
        }

        return $this;
    }

}
