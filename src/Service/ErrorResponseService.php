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
     * @param ErrorMessageDTO[] $causes         the causes of the error
     * @param string            $detailCode     the detailed code of the error
     * @param ErrorMessageDTO[] $messages       the error messages
     * @param int               $httpStatusCode the HTTP status code
     *
     * @return JsonResponse the JSON response
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
     * @param string $locale       the locale of the message
     * @param string $localeOrigin the origin of the locale
     * @param string $text         the text of the message
     *
     * @return ErrorMessageDTO the error message DTO
     */
    private function createErrorMessageDto(string $locale, string $localeOrigin, string $text): ErrorMessageDTO
    {
        return new ErrorMessageDTO($locale, $localeOrigin, $text);
    }

    /**
     * Handles an error and creates a JSON response.
     *
     * @param string $message        the error message
     * @param string $detailCode     the detailed code of the error
     * @param int    $httpStatusCode the HTTP status code
     *
     * @return JsonResponse the JSON response
     */
    public function handleError(string $message, string $detailCode, int $httpStatusCode): JsonResponse
    {
        $errorMessageDTO = $this->createErrorMessageDto('en-US', 'DEFAULT', $message);

        return $this->createErrorResponse([$errorMessageDTO], $detailCode, [$errorMessageDTO], $httpStatusCode);
    }
}
