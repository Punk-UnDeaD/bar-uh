<?php

declare(strict_types=1);

namespace App\Controller;

use JetBrains\PhpStorm\Pure;
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
        return 'command' === $argument->getName();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $argument->getType();
        $reflection = new \ReflectionClass($class);
        $parameters = array_map(
            fn ($parameter) => $parameter->getName(),
            $reflection->getConstructor()->getParameters()
        );
        $data = $this->extractData($request);

        if (in_array('key', $parameters) && in_array('value', $parameters) && is_array($data) && count($data) === 1) {
            $data = [
                'key'   => array_key_first($data),
                'value' => reset($data),
            ];
        }

        $commandArguments = [];
        $lastArgument = null;
        foreach ($parameters as $parameter) {
            if ($request->attributes->has($parameter)) {
                $commandArguments[$parameter] = $request->attributes->get($parameter);
            } else {
                $lastArgument = $parameter;
            }
        }

        if ((count($commandArguments) === (count($parameters) - 1)) && $lastArgument === 'value') {
            $commandArguments[$lastArgument] = $data;
        } else {
            $commandArguments = array_merge($commandArguments, array_intersect_key($data, array_flip($parameters)));
        }


        yield new $class(...$commandArguments);
    }

    private function extractData(Request $request)
    {
        return match (true) {
            ('json' === $request->getContentType()) => json_decode($request->getContent(), true),
            str_starts_with(
                $request->headers->get('content-type') ?? '',
                'multipart/form-data;'
            ) => $request->request->all(),
            default => $request->getContent()
        };
    }

}
