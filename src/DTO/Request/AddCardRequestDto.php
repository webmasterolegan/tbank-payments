<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\CardCheckTypeEnum;

/** Запрос AddCard — инициировать привязку карты. */
final readonly class AddCardRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $customerKey,
        public CardCheckTypeEnum $checkType = CardCheckTypeEnum::No,
        public ?string $description = null,
        public ?string $notificationUrl = null,
        public ?string $successUrl = null,
        public ?string $failUrl = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'CustomerKey'     => $this->customerKey,
            'CheckType'       => $this->checkType->value,
            'Description'     => $this->description,
            'NotificationURL' => $this->notificationUrl,
            'SuccessURL'      => $this->successUrl,
            'FailURL'         => $this->failUrl,
        ]);
    }
}
