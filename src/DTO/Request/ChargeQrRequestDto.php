<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос ChargeQr — списание по привязанному счёту СБП. */
final readonly class ChargeQrRequestDto
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
        $params = [
            'PaymentId'    => $this->paymentId,
            'AccountToken' => $this->accountToken,
        ];

        if ($this->ip !== null) {
            $params['IP'] = $this->ip;
        }
        if ($this->sendEmail !== null) {
            $params['SendEmail'] = $this->sendEmail;
        }
        if ($this->infoEmail !== null) {
            $params['InfoEmail'] = $this->infoEmail;
        }

        return $params;
    }
}
