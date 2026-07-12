<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос ChargeQr — списание по привязанному счёту СБП. */
final readonly class ChargeQrRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $paymentId,
        public string $accountToken,
        public ?string $ip = null,
        public ?bool $sendEmail = null,
        public ?string $infoEmail = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId'    => $this->paymentId,
            'AccountToken' => $this->accountToken,
            'IP'           => $this->ip,
            'SendEmail'    => $this->sendEmail,
            'InfoEmail'    => $this->infoEmail,
        ]);
    }
}
