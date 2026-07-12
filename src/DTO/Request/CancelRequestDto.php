<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;

/**
 * Запрос Cancel — отмена / частичный возврат платежа.
 */
final readonly class CancelRequestDto
{
    public function __construct(
        public string $paymentId,
        /** Сумма в копейках; null — полная отмена. */
        public ?int $amount = null,
        /** Чек возврата (требуется при частичном возврате с ФФД). */
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
