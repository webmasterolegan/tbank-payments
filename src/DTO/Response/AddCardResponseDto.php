<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

final readonly class AddCardResponseDto
{
    public function __construct(
        public bool $success,
        public string $customerKey,
        public string $requestKey,
        /** URL для привязки карты (редирект покупателя). */
        public ?string $paymentUrl = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success     : ApiValueParser::parseSuccess($data['Success'] ?? false),
            customerKey : ApiValueParser::asString($data['CustomerKey'] ?? null),
            requestKey  : ApiValueParser::asString($data['RequestKey'] ?? null),
            paymentUrl  : ApiValueParser::asNullableString($data['PaymentURL'] ?? null),
            errorCode   : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message     : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
