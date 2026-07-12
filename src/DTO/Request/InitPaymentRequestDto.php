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
final readonly class InitPaymentRequestDto
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
        $params = [
            'Amount'  => $this->amount,
            'OrderId' => $this->orderId,
        ];

        if ($this->description !== null) {
            $params['Description'] = $this->description;
        }
        if ($this->customerKey !== null) {
            $params['CustomerKey'] = $this->customerKey;
        }
        if ($this->recurrent) {
            $params['Recurrent'] = 'Y';
        }
        if ($this->payType !== null) {
            $params['PayType'] = $this->payType->value;
        }
        if ($this->language !== null) {
            $params['Language'] = $this->language->value;
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
        if ($this->receipt !== null) {
            $params['Receipt'] = $this->receipt->toArray();
        }
        if ($this->data !== []) {
            $params['DATA'] = $this->data;
        }

        return $params;
    }
}
