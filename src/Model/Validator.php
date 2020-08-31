<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $object)
    {
        $violations = $this->validator->validate($object);

        if ($violations->count()) {
            throw new ValidationFailedException($object, $violations);
        }
    }
}
