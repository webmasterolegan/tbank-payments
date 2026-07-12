<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Платёж внутри заказа (CheckOrder.Payments). */
final readonly class OrderPaymentDto
{
    public function __construct(
        public string $paymentId,
        public PaymentStatusEnum $status,
        public int $amount,
        public ?string $rrn = null,
        public ?string $sbpPaymentId = null,
        public ?string $sbpCustomerId = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentId    : ApiValueParser::asString($data['PaymentId'] ?? null),
            status       : ApiValueParser::asPaymentStatus($data['Status'] ?? null),
            amount       : ApiValueParser::asInt($data['Amount'] ?? null),
            rrn          : ApiValueParser::asNullableString($data['RRN'] ?? null),
            sbpPaymentId : ApiValueParser::asNullableString($data['SbpPaymentId'] ?? null),
            sbpCustomerId: ApiValueParser::asNullableString($data['SbpCustomerId'] ?? null),
        );
    }
}
