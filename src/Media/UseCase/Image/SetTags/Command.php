<?php

declare(strict_types=1);

namespace App\Media\UseCase\Image\SetTags;

class Command
{
    /** @var list<string> */
    public array $tags;

    /**
     * @param list<string>|string $value
     */
    public function __construct(public string $image, array|string $value)
    {
        if (is_string($value)) {
            $value = array_filter(array_map('trim', explode(',', $value)));
        }
        /** @var list<string> $value */
        $this->tags = $value;
    }
}
