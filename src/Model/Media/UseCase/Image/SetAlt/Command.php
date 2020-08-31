<?php

declare(strict_types=1);

namespace App\Model\Media\UseCase\Image\SetAlt;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\Regex(pattern: "/\.$/", message: "точка в конце где?")]
    #[Assert\Regex(pattern: "/^-/", match: false, message: 'минус в ночале' )]
    public ?string $alt;

    public function __construct(public string $image, ?string $value)
    {
        $this->alt = $value;
    }
}
