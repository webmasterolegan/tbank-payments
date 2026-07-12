<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос Charge — списание по сохранённым реквизитам (RebillId). */
final readonly class ChargeRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $paymentId,
        public string $rebillId,
        public ?string $ip = null,
        public ?bool $sendEmail = null,
        public ?string $infoEmail = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId'  => $this->paymentId,
            'RebillId'   => $this->rebillId,
            'IP'         => $this->ip,
            'SendEmail'  => $this->sendEmail,
            'InfoEmail'  => $this->infoEmail,
        ]);
    }
}
