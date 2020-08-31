<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseHandler
{
    #[Required]
    public Flusher $flusher;

    #[Required]
    public Validator $validator;

    public function __construct(protected GetOneRepositoryInterface $repository)
    {
    }

    public function __invoke(object $command): void
    {
        $this->validator->validate($command);
        $object = $this->repository->get($command->id);
        $this->handle($object, $command);
        $this->flusher->flush();
    }

    abstract function handle(object $object, object $command);
}
