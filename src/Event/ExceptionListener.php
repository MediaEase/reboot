<?php

declare(strict_types=1);

/*
 * This file is part of the MediaEase project.
 *
 * (c) Thomas Chauveau <contact.tomc@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Event;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ErrorResponseService;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Listener for handling kernel exceptions.
 */
final class ExceptionListener
{
    /**
     * Constructor.
     *
     * @param ErrorResponseService $errorResponseService The service for creating error responses.
     * @param LoggerInterface $logger The logger.
     */
    public function __construct(private ErrorResponseService $errorResponseService, private LoggerInterface $logger)
    {
    }

    /**
     * Handles kernel exceptions and converts them to JSON responses.
     *
     * @param ExceptionEvent $exceptionEvent The exception event.
     */
    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $throwable = $exceptionEvent->getThrowable();

        $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);

        $statusCode = $throwable instanceof HttpExceptionInterface ? $throwable->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $detailCode = match ($statusCode) {
            JsonResponse::HTTP_BAD_REQUEST => '400_BAD_REQUEST',
            JsonResponse::HTTP_UNAUTHORIZED => '401_UNAUTHORIZED',
            JsonResponse::HTTP_FORBIDDEN => '403_FORBIDDEN',
            JsonResponse::HTTP_NOT_FOUND => '404_NOT_FOUND',
            JsonResponse::HTTP_METHOD_NOT_ALLOWED => '405_METHOD_NOT_ALLOWED',
            JsonResponse::HTTP_TOO_MANY_REQUESTS => '429_TOO_MANY_REQUESTS',
            default => '500_INTERNAL_SERVER_ERROR',
        };

        $response = $this->errorResponseService->handleError($throwable->getMessage(), $detailCode, $statusCode);
        $exceptionEvent->setResponse($response);
    }
}
