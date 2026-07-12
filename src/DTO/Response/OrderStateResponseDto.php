<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

final readonly class OrderStateResponseDto
{
    /** @param OrderPaymentDto[] $payments */
    public function __construct(
        public bool $success,
        public string $orderId,
        public array $payments = [],
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $payments = [];
        foreach ((array) ($data['Payments'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }

            /** @var array<string, mixed> $item */
            $payments[] = OrderPaymentDto::fromArray($item);
        }

        return new self(
            success   : ApiValueParser::parseSuccess($data['Success'] ?? false),
            orderId   : ApiValueParser::asString($data['OrderId'] ?? null),
            payments  : $payments,
            errorCode : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message   : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
