<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;

/**
 * Запрос Confirm — подтверждение двухстадийного платежа.
 */
final readonly class ConfirmRequestDto
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
        $params = ['PaymentId' => $this->paymentId];

        if ($this->amount !== null) {
            $params['Amount'] = $this->amount;
        }
        if ($this->receipt !== null) {
            $params['Receipt'] = $this->receipt->toArray();
        }

        return $params;
    }
}
