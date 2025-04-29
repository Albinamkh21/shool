<?php
/*
Author
*/

namespace App\Infrastructure\Symfony\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotNormalizableValueException ||
            $exception instanceof MissingConstructorArgumentsException
        ) {
            $event->setResponse(new JsonResponse([
                'error' => 'Invalid request data',
                'details' => $exception->getMessage(),
            ], 400));
        }

        if ($exception instanceof BadRequestHttpException) {
            $event->setResponse(new JsonResponse([
                'message' => 'Bad Request',
                'details' => $exception->getMessage(),
            ], 400));
        }
    }
}