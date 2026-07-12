<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;

/**
 * Запрос Confirm — подтверждение двухстадийного платежа.
 */
final readonly class ConfirmRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $paymentId,
        /** Сумма в копейках; если null — подтверждается исходная сумма. */
        public ?int $amount = null,
        public ?ReceiptDto $receipt = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId' => $this->paymentId,
            'Amount'    => $this->amount,
            'Receipt'   => $this->receipt?->toArray(),
        ]);
    }
}
