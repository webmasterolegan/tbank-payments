<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\CardCheckTypeEnum;

/** Запрос AddCard — инициировать привязку карты. */
final readonly class AddCardRequestDto
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
        $params = [
            'CustomerKey' => $this->customerKey,
            'CheckType'   => $this->checkType->value,
        ];

        if ($this->description !== null) {
            $params['Description'] = $this->description;
        }
        if ($this->notificationUrl !== null) {
            $params['NotificationURL'] = $this->notificationUrl;
        }
        if ($this->successUrl !== null) {
            $params['SuccessURL'] = $this->successUrl;
        }
        if ($this->failUrl !== null) {
            $params['FailURL'] = $this->failUrl;
        }

        return $params;
    }
}
