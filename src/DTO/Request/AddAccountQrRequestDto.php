<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\Enum\QrDataTypeEnum;

/** Запрос AddAccountQr — привязка счёта покупателя через СБП. */
final readonly class AddAccountQrRequestDto
{
    /**
     * @param array<string, string> $data Дополнительные параметры (DATA).
     */
    public function __construct(
        public string $description,
        public QrDataTypeEnum $dataType = QrDataTypeEnum::Payload,
        public array $data = [],
        public ?string $redirectDueDate = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [
            'Description' => $this->description,
            'DataType'    => $this->dataType->value,
        ];

        if ($this->data !== []) {
            $params['DATA'] = $this->data;
        }
        if ($this->redirectDueDate !== null) {
            $params['RedirectDueDate'] = $this->redirectDueDate;
        }

        return $params;
    }
}
