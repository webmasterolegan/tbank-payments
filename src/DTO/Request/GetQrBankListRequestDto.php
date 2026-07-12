<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

use TBank\Payments\DTO\Shared\DeviceDto;
use TBank\Payments\Enum\QrScenarioTypeEnum;

/** Запрос GetQrBankList — список банков-участников СБП. */
final readonly class GetQrBankListRequestDto extends BaseRequestDto
{
    public function __construct(
        public DeviceDto $device,
        public QrScenarioTypeEnum $scenarioType = QrScenarioTypeEnum::Qr,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'ScenarioType' => $this->scenarioType->value,
            'Device'       => $this->device->toArray(),
        ];
    }
}
