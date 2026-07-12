<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\NotificationTypeEnum;

/** Запрос Resend — повторная отправка неотправленных уведомлений. */
final readonly class ResendRequestDto
{
    public function __construct(
        public ?string $paymentId = null,
        public ?NotificationTypeEnum $notificationType = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        if ($this->paymentId !== null) {
            $params['PaymentId'] = $this->paymentId;
        }
        if ($this->notificationType !== null) {
            $params['NotificationTypes'] = $this->notificationType->value;
        }

        return $params;
    }
}
