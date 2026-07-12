<?php

declare(strict_types=1);

namespace TBank\Payments\Exceptions;

/** Ошибка, возвращённая T-Bank API (Success=false). */
final class ApiException extends TBankException
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly mixed $details = null,
        private readonly int $httpCode = 200,
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
