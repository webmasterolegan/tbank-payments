<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода FinishAuthorize. */
final readonly class FinishAuthorizeResponseDto
{
    public function __construct(
        public bool $success,
        public PaymentStatusEnum $status,
        public string $paymentId,
        public string $orderId,
        public int $amount,
        /** ACS URL для редиректа при 3DS (если нужна верификация). */
        public ?string $acsUrl = null,
        public ?string $md = null,
        public ?string $paReq = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success   : ApiValueParser::parseSuccess($data['Success'] ?? false),
            status    : ApiValueParser::asPaymentStatus($data['Status'] ?? null),
            paymentId : ApiValueParser::asString($data['PaymentId'] ?? null),
            orderId   : ApiValueParser::asString($data['OrderId'] ?? null),
            amount    : ApiValueParser::asInt($data['Amount'] ?? null),
            acsUrl    : ApiValueParser::asNullableString($data['ACSUrl'] ?? null),
            md        : ApiValueParser::asNullableString($data['MD'] ?? null),
            paReq     : ApiValueParser::asNullableString($data['PaReq'] ?? null),
            errorCode : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message   : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }

    public function requires3ds(): bool
    {
        return $this->acsUrl !== null && $this->acsUrl !== '';
    }
}
