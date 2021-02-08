<?php

declare(strict_types=1);

namespace App\Controller;

use Generator;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CommandValueResolver implements ArgumentValueResolverInterface
{
    #[Pure]
    public function supports(
        Request $request,
        ArgumentMetadata $argument
    ): bool {
        /** @psalm-suppress ImpureMethodCall */
        return 'command' === $argument->getName();
    }

    /**
     * @template T
     *
     * @return \Generator<T>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var class-string<T> $class */
        $class = $argument->getType();

        $reflection = new ReflectionClass($class);
        $parameters = array_map(
            fn ($parameter) => $parameter->getName(),
            ($reflection->getConstructor()?->getParameters() ?: [])
        );
        $data = $this->extractData($request);

        if (in_array('key', $parameters) && in_array('value', $parameters) && is_array($data) && 1 === count($data)) {
            $data = [
                'key'   => array_key_first($data),
                'value' => reset($data),
            ];
        }

        $commandArguments = [];
        $lastArgument = null;
        foreach ($parameters as $parameter) {
            if ($request->attributes->has($parameter)) {
                /** @psalm-suppress MixedAssignment */
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

        /** @psalm-suppress MixedMethodCall */
        yield new $class(...$commandArguments);
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     *
     * @return array<array-key, mixed>|null|scalar
     */
    private function extractData(Request $request): array | bool | int | float | string | null
    {
        /** @psalm-suppress MixedReturnStatement */
        return match (true) {
            ('json' === $request->getContentType()) => json_decode($request->getContent(), true),
            str_starts_with(
                $request->headers->get('content-type') ?? '',
                'multipart/form-data;'
            )       => $request->request->all(),
            default => $request->getContent()
        };
    }
}
