<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use Generator;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AutoconfigureTag('controller.argument_value_resolver')]
class CommandValueResolver implements ArgumentValueResolverInterface
{
    #[Pure]
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return 'command' === $argument->getName();
    }

    /**
     * @return \Generator<object>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var class-string $class */
        $class = $argument->getType();

        $reflection = new ReflectionClass($class);
        $parameters = array_map(
            fn($parameter) => $parameter->getName(),
            ($reflection->getConstructor()?->getParameters() ?: [])
        );

        /** @psalm-var mixed $data */
        $data = $this->extractData($request);

        if (in_array('key', $parameters) && in_array('value', $parameters) && is_array($data) && 1 === count($data)) {
            $data = [
                'key'   => array_key_first($data),
                'value' => reset($data),
            ];
        }

        /** @psalm-var array<string, mixed> $commandArguments */
        $commandArguments = [];
        $lastArgument = null;
        foreach ($parameters as $parameter) {
            if ($request->attributes->has($parameter)) {
                $commandArguments[$parameter] = $request->attributes->get($parameter);
            } else {
                $lastArgument = $parameter;
            }
        }

        if ((count($commandArguments) === (count($parameters) - 1)) && 'value' === $lastArgument) {
            $commandArguments[$lastArgument] = $data;
        } elseif ($data && is_array($data)) {
            $commandArguments = array_merge($commandArguments, array_intersect_key($data, array_flip($parameters)));
        }

        yield new $class(...$commandArguments);
    }

    private function extractData(Request $request): mixed
    {
        return match (true) {
            ('json' === $request->getContentType()) => $request->toArray(),
            str_starts_with(
                $request->headers->get('content-type') ?? '',
                'multipart/form-data;'
            )       => $request->request->all(),
            default => $request->getContent()
        };
    }
}
