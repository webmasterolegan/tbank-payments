<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода Resend. */
final readonly class ResendResponseDto
{
    public function __construct(
        public bool $success,
        public int $count,
        public ?PaymentStatusEnum $status = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $statusRaw = $data['Status'] ?? null;

        return new self(
            success  : ApiValueParser::parseSuccess($data['Success'] ?? false),
            count    : ApiValueParser::asInt($data['Count'] ?? null),
            status   : is_string($statusRaw) || is_int($statusRaw)
                ? ApiValueParser::asPaymentStatus($statusRaw)
                : null,
            errorCode: ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message  : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
