<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос RemoveCard — удалить привязанную карту. */
final readonly class RemoveCardRequestDto
{
    public function __construct(
        public string $customerKey,
        public string $cardId,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'CustomerKey' => $this->customerKey,
            'CardId'      => $this->cardId,
        ];
    }
}
