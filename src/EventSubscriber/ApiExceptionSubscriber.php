<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (strpos($request->getPathInfo(), "/api/") !== 0) {
            return;
        }
        if (strpos($request->getPathInfo(), "/api/garden/search") === 0) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $response = new JsonResponse(
                ["error" => $exception->getMessage()],
                $exception->getStatusCode()
            );
        } else {
            $response = new JsonResponse(
                ["error" => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
