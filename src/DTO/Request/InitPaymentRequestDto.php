<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\ReceiptDto;
use TBank\Payments\Enum\{LanguageEnum, PayTypeEnum};

/**
 * Запрос для метода Init — инициирование платежа.
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
final readonly class InitPaymentRequestDto extends BaseRequestDto
{
    /**
     * @param array<string,string> $data Дополнительные данные (DATA).
     */
    public function __construct(
        public int $amount,
        public string $orderId,
        public ?string $description = null,
        public ?string $customerKey = null,
        public bool $recurrent = false,
        public ?PayTypeEnum $payType = null,
        public ?LanguageEnum $language = null,
        public ?string $notificationUrl = null,
        public ?string $successUrl = null,
        public ?string $failUrl = null,
        public ?ReceiptDto $receipt = null,
        public array $data = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'Amount'          => $this->amount,
            'OrderId'         => $this->orderId,
            'Description'     => $this->description,
            'CustomerKey'     => $this->customerKey,
            'Recurrent'       => $this->recurrent ? 'Y' : null,
            'PayType'         => $this->payType?->value,
            'Language'        => $this->language?->value,
            'NotificationURL' => $this->notificationUrl,
            'SuccessURL'      => $this->successUrl,
            'FailURL'         => $this->failUrl,
            'Receipt'         => $this->receipt?->toArray(),
            'DATA'            => $this->data !== [] ? $this->data : null,
        ]);
    }
}
