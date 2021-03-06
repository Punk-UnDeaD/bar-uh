<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Infrastructure\Model\EntityNotFoundException;
use App\Infrastructure\ReadModel\ReadModelNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AutoconfigureTag('kernel.event_listener', ['event' => 'kernel.exception', 'priority' => 5000])]
class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ('json' === $event->getRequest()->getRequestFormat()) {
            $data = ['status' => 'error', 'message' => $exception->getMessage()];
            $headers = ['Content-type' => 'application/json'];
            switch (true) {
                case $exception instanceof ReadModelNotFoundException:
                case $exception instanceof EntityNotFoundException:
                    $code = 404;
                    break;

                case $exception instanceof ValidationFailedException:
                case $exception instanceof MessengerValidationFailedException:
                    $code = 400;
                    $data['message'] = 'Validation error.';
                    $data['errors'] = array_map(
                        fn(ConstraintViolationInterface $violation): array => [
                            'propertyPath' => $violation->getPropertyPath(),
                            'message'      => $violation->getMessageTemplate(),
                            'parameters'   => $violation->getParameters(),
                        ],
                        [...$exception->getViolations()]
                    );
                    break;
                case $exception instanceof AccessDeniedException:
                    $code = 403;
                    break;
                case $exception instanceof HttpException:
                    $code = $exception->getStatusCode();
                    $headers = $exception->getHeaders();
                    break;
                default:
                    $data['message'] = 'Application unavailable.';
                    $code = 500;
            }

            $response = new JsonResponse($data, $code, $headers);
            $response->headers->set('Content-Type', 'application/problem+json');
            $event->setResponse($response);
        }
    }
}
