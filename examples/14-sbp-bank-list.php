<?php

declare(strict_types=1);

/**
 * Пример: список банков-участников СБП (GetQrBankList).
 *
 * Запуск:
 *   php examples/14-sbp-bank-list.php
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\GetQrBankListRequestDto;
use TBank\Payments\DTO\Shared\DeviceDto;
use TBank\Payments\Enum\DeviceTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

try {
    $banks = $client->sbp()->getQrBankList(
        new GetQrBankListRequestDto(
            device: new DeviceDto(DeviceTypeEnum::Desktop, 'Linux'),
        ),
    );

    foreach ($banks->bankList as $bank) {
        echo "{$bank->bankOrder}. {$bank->bankName} ({$bank->nspkBankId})\n";
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
