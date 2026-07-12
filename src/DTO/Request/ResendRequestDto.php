<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\NotificationTypeEnum;

/** Запрос Resend — повторная отправка неотправленных уведомлений. */
final readonly class ResendRequestDto extends BaseRequestDto
{
    public function __construct(
        public ?string $paymentId = null,
        public ?NotificationTypeEnum $notificationType = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId'         => $this->paymentId,
            'NotificationTypes' => $this->notificationType?->value,
        ]);
    }
}
