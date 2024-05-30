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

namespace App\Service;

use App\DTO\ErrorMessageDTO;
use App\DTO\ErrorResponseDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Service for creating error responses.
 */
final class ErrorResponseService
{
    /**
     * Creates an error response.
     *
     * @param ErrorMessageDTO[] $causes The causes of the error.
     * @param string $detailCode The detailed code of the error.
     * @param ErrorMessageDTO[] $messages The error messages.
     * @param int $httpStatusCode The HTTP status code.
     * @return JsonResponse The JSON response.
     */
    public function createErrorResponse(
        array $causes,
        string $detailCode,
        array $messages,
        int $httpStatusCode
    ): JsonResponse {
        $trackingId = uniqid();

        $errorResponseDTO = new ErrorResponseDTO();
        $errorResponseDTO->setCauses($causes)
                        ->setDetailCode($detailCode)
                        ->setMessages($messages)
                        ->setTrackingId($trackingId)
                        ->setCode($httpStatusCode);

        return new JsonResponse($errorResponseDTO, $httpStatusCode);
    }

    /**
     * Creates an error message DTO.
     *
     * @param string $locale The locale of the message.
     * @param string $localeOrigin The origin of the locale.
     * @param string $text The text of the message.
     * @return ErrorMessageDTO The error message DTO.
     */
    private function createErrorMessageDto(string $locale, string $localeOrigin, string $text): ErrorMessageDTO
    {
        return new ErrorMessageDTO($locale, $localeOrigin, $text);
    }

    /**
     * Handles an error and creates a JSON response.
     *
     * @param string $message The error message.
     * @param string $detailCode The detailed code of the error.
     * @param int $httpStatusCode The HTTP status code.
     * @return JsonResponse The JSON response.
     */
    public function handleError(string $message, string $detailCode, int $httpStatusCode): JsonResponse
    {
        $errorMessageDTO = $this->createErrorMessageDto('en-US', 'DEFAULT', $message);

        return $this->createErrorResponse([$errorMessageDTO], $detailCode, [$errorMessageDTO], $httpStatusCode);
    }
}
