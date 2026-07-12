<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;

/** Запрос SendClosingReceipt — закрывающий чек. */
final readonly class SendReceiptRequestDto
{
    public function __construct(
        public string $paymentId,
        public ReceiptDto $receipt,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'PaymentId' => $this->paymentId,
            'Receipt'   => $this->receipt->toArray(),
        ];
    }
}
