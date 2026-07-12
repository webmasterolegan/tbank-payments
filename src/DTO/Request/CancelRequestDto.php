<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;

/**
 * Запрос Cancel — отмена / частичный возврат платежа.
 */
final readonly class CancelRequestDto extends BaseRequestDto
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
        return $this->filterNulls([
            'PaymentId' => $this->paymentId,
            'Amount'    => $this->amount,
            'Receipt'   => $this->receipt?->toArray(),
        ]);
    }
}
