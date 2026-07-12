<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\QrDataTypeEnum;

/** Запрос GetQr — формирование QR для оплаты через СБП. */
final readonly class GetQrRequestDto
{
    public function __construct(
        public string $paymentId,
        public QrDataTypeEnum $dataType = QrDataTypeEnum::Payload,
        public ?string $bankId = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [
            'PaymentId' => $this->paymentId,
            'DataType'  => $this->dataType->value,
        ];

        if ($this->bankId !== null) {
            $params['BankId'] = $this->bankId;
        }

        return $params;
    }
}
