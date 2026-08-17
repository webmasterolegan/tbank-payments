<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Shared;

/**
 * Данные магазина маркетплейса (элемент Init.Shops).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
final readonly class ShopDto
{
    private const int MAX_NAME_LENGTH = 128;

    public function __construct(
        /** Код магазина (Submerchant_ID). */
        public string $shopCode,
        /** Сумма в копейках, относящаяся к ShopCode. */
        public int $amount,
        /** Наименование товара. */
        public ?string $name = null,
        /**
         * Комиссия маркетплейса в копейках.
         * Если null — используется комиссия, указанная при регистрации.
         */
        public ?int $fee = null,
    ) {
        if ($this->shopCode === '') {
            throw new \InvalidArgumentException('Shop code must not be empty');
        }

        if ($this->amount < 0) {
            throw new \InvalidArgumentException('Shop amount must not be negative');
        }

        if ($this->fee !== null && $this->fee < 0) {
            throw new \InvalidArgumentException('Shop fee must not be negative');
        }

        if ($this->name === null) {
            return;
        }

        if ($this->name === '') {
            throw new \InvalidArgumentException('Shop name must not be empty');
        }

        if (!mb_check_encoding($this->name, 'UTF-8')) {
            throw new \InvalidArgumentException('Shop name must be valid UTF-8');
        }

        if (mb_strlen($this->name, 'UTF-8') > self::MAX_NAME_LENGTH) {
            throw new \InvalidArgumentException(sprintf(
                'Shop name must not exceed %d characters',
                self::MAX_NAME_LENGTH,
            ));
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'ShopCode' => $this->shopCode,
            'Amount'   => $this->amount,
            'Name'     => $this->name,
            'Fee'      => $this->fee !== null ? (string) $this->fee : null,
        ], static fn(mixed $v): bool => $v !== null);
    }
}
