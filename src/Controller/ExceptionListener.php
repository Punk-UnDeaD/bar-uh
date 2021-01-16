<?php

declare(strict_types=1);

namespace App\Controller;

use App\ReadModel\ReadModelNotFoundException;
use App\Model\EntityNotFoundException;
use http\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
                    $violations = $exception->getViolations();
                    foreach ($violations as $violation) {
                        $data['errors'][] = [
                            'propertyPath' => $violation->getPropertyPath(),
                            'message'      => $violation->getMessageTemplate(),
                            'parameters'   => $violation->getParameters(),
                        ];
                    }
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
