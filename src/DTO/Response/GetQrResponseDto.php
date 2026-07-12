<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода GetQr. */
final readonly class GetQrResponseDto
{
    public function __construct(
        public bool $success,
        public string $paymentId,
        /** Payload СБП или SVG-изображение QR. */
        public ?string $data = null,
        public PaymentStatusEnum $status = PaymentStatusEnum::Unknown,
        public ?string $terminalKey = null,
        public ?string $orderId = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success     : ApiValueParser::parseSuccess($data['Success'] ?? false),
            paymentId   : ApiValueParser::asString($data['PaymentId'] ?? null),
            data        : ApiValueParser::asNullableString($data['Data'] ?? null),
            status      : ApiValueParser::asPaymentStatus($data['Status'] ?? null),
            terminalKey : ApiValueParser::asNullableString($data['TerminalKey'] ?? null),
            orderId     : ApiValueParser::asNullableString($data['OrderId'] ?? null),
            errorCode   : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message     : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }

    public function hasQrData(): bool
    {
        return $this->data !== null && $this->data !== '';
    }
}
