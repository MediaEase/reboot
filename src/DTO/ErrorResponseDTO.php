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

namespace App\DTO;

use App\Interface\ApiResponseInterface;
use App\Interface\ErrorResponseInterface;

/**
 * Data Transfer Object for error responses.
 */
final class ErrorResponseDTO implements ApiResponseInterface, ErrorResponseInterface
{
    public string $error;

    public int $code;

    public ?array $data = [];

    /** @var ErrorMessageDTO[] */
    public array $causes = [];

    public string $detailCode;

    /** @var ErrorMessageDTO[] */
    public array $messages = [];

    public string $trackingId;

    /**
     * Gets the error message.
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Sets the error message.
     */
    public function setError(string $error): self
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Gets the HTTP status code.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Sets the HTTP status code.
     */
    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets additional data.
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Sets additional data.
     */
    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the causes of the error.
     *
     * @return ErrorMessageDTO[]
     */
    public function getCauses(): array
    {
        return $this->causes;
    }

    /**
     * Sets the causes of the error.
     *
     * @param ErrorMessageDTO[] $causes
     */
    public function setCauses(array $causes): self
    {
        $this->causes = $causes;

        return $this;
    }

    /**
     * Gets the detailed code of the error.
     */
    public function getDetailCode(): string
    {
        return $this->detailCode;
    }

    /**
     * Sets the detailed code of the error.
     */
    public function setDetailCode(string $detailCode): self
    {
        $this->detailCode = $detailCode;

        return $this;
    }

    /**
     * Gets the error messages.
     *
     * @return ErrorMessageDTO[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Sets the error messages.
     *
     * @param ErrorMessageDTO[] $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Gets the tracking ID of the error.
     */
    public function getTrackingId(): string
    {
        return $this->trackingId;
    }

    /**
     * Sets the tracking ID of the error.
     */
    public function setTrackingId(string $trackingId): self
    {
        $this->trackingId = $trackingId;

        return $this;
    }
}
