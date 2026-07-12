<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\QrDataTypeEnum;

/** Запрос GetQr — формирование QR для оплаты через СБП. */
final readonly class GetQrRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $paymentId,
        public QrDataTypeEnum $dataType = QrDataTypeEnum::Payload,
        public ?string $bankId = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId' => $this->paymentId,
            'DataType'  => $this->dataType->value,
            'BankId'    => $this->bankId,
        ]);
    }
}
