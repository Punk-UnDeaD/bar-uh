<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\SetTags;

class Command
{

    public array $tags;

    public function __construct(public string $image, array|string $value)
    {
        if (is_string($value)) {
            $value = array_filter(array_map('trim', explode(',', $value)));
        }
        $this->tags = $value;
    }

}
