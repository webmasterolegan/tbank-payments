<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Shared;

use TBank\Payments\Enum\Fiscal\{PaymentMethodEnum, PaymentObjectEnum, VatEnum};

/** Позиция в чеке (ФФД). */
final readonly class ReceiptItemDto
{
    public function __construct(
        public string $name,
        /** Цена в копейках */
        public int $price,
        public float $quantity,
        /** Сумма = price * quantity, в копейках */
        public int $amount,
        public VatEnum $tax,
        public ?string $ean13 = null,
        public ?PaymentObjectEnum $paymentObject = null,
        public ?PaymentMethodEnum $paymentMethod = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'Name'          => $this->name,
            'Price'         => $this->price,
            'Quantity'      => $this->quantity,
            'Amount'        => $this->amount,
            'Tax'           => $this->tax->value,
            'Ean13'         => $this->ean13,
            'PaymentObject' => $this->paymentObject?->value,
            'PaymentMethod' => $this->paymentMethod?->value,
        ], fn(mixed $v): bool => $v !== null);
    }
}
