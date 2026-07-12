<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

final readonly class PaymentStateResponseDto
{
    public function __construct(
        public bool $success,
        public string $terminalKey,
        public PaymentStatusEnum $status,
        public string $paymentId,
        public string $orderId,
        public int $amount,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success     : ApiValueParser::parseSuccess($data['Success'] ?? false),
            terminalKey : ApiValueParser::asString($data['TerminalKey'] ?? null),
            status      : ApiValueParser::asPaymentStatus($data['Status'] ?? null),
            paymentId   : ApiValueParser::asString($data['PaymentId'] ?? null),
            orderId     : ApiValueParser::asString($data['OrderId'] ?? null),
            amount      : ApiValueParser::asInt($data['Amount'] ?? null),
            errorCode   : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message     : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }

    public function isPaid(): bool
    {
        return $this->status->isSuccessful();
    }
}
