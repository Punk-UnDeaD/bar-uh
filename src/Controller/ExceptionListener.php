<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\EntityNotFoundException;
use App\ReadModel\ReadModelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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
                        fn (ConstraintViolationInterface $violation) => [
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
            $event->setResponse(new JsonResponse($data, $code, $headers));
        }
    }
}
