<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

final readonly class SendReceiptResponseDto
{
    public function __construct(
        public bool $success,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success   : ApiValueParser::parseSuccess($data['Success'] ?? false),
            errorCode : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message   : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
