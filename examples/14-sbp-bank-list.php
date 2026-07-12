<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\GetQrBankListRequestDto;
use TBank\Payments\DTO\Shared\DeviceDto;
use TBank\Payments\Enum\DeviceTypeEnum;

$banks = $client->sbp()->getQrBankList(
    new GetQrBankListRequestDto(
        device: new DeviceDto(DeviceTypeEnum::Desktop, 'Linux'),
    ),
);

foreach ($banks->bankList as $bank) {
    echo "{$bank->bankOrder}. {$bank->bankName} ({$bank->nspkBankId})\n";
}
