<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода Init. */
final readonly class InitPaymentResponseDto
{
    public function __construct(
        public bool $success,
        public string $terminalKey,
        public PaymentStatusEnum $status,
        public string $paymentId,
        public string $orderId,
        public int $amount,
        /** URL платёжной формы T-Bank для редиректа покупателя. */
        public ?string $paymentUrl = null,
        public ?string $errorCode = null,
        public ?string $message = null,
        public ?string $details = null,
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
            paymentUrl  : ApiValueParser::asNullableString($data['PaymentURL'] ?? null),
            errorCode   : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message     : ApiValueParser::asNullableString($data['Message'] ?? null),
            details     : ApiValueParser::asNullableString($data['Details'] ?? null),
        );
    }

    public function hasPaymentUrl(): bool
    {
        return $this->paymentUrl !== null && $this->paymentUrl !== '';
    }
}
