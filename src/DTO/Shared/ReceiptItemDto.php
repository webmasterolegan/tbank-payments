<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Shared;

use TBank\Payments\Enum\Fiscal\{PaymentMethodEnum, PaymentObjectEnum, VatEnum};

/** Позиция в чеке (ФФД). */
final readonly class ReceiptItemDto
{
    private const int MAX_NAME_LENGTH = 128;

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
    ) {
        if ($this->price < 0) {
            throw new \InvalidArgumentException('Receipt item price must not be negative');
        }

        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Receipt item amount must not be negative');
        }

        if (!mb_check_encoding($this->name, 'UTF-8')) {
            throw new \InvalidArgumentException('Receipt item name must be valid UTF-8');
        }

        // Лимит API — 128 символов (не байт): в UTF-8 кириллица занимает 2 байта на символ.
        if (mb_strlen($this->name, 'UTF-8') > self::MAX_NAME_LENGTH) {
            throw new \InvalidArgumentException(sprintf(
                'Receipt item name must not exceed %d characters',
                self::MAX_NAME_LENGTH,
            ));
        }
    }

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
