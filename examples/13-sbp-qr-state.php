<?php

declare(strict_types=1);

/**
 * Пример: статус СБП-платежа (GetQrState).
 *
 * Запуск:
 *   php examples/13-sbp-qr-state.php <PaymentId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$paymentId = $argv[1] ?? null;

if ($paymentId === null) {
    fwrite(STDERR, "Usage: php examples/13-sbp-qr-state.php <PaymentId>\n");
    exit(1);
}

$client = createClient();

try {
    $state = $client->sbp()->getQrState($paymentId);

    echo "Status:  {$state->status->value}\n";
    echo "Amount:  {$state->amount} коп.\n";

    if ($state->status === PaymentStatusEnum::Confirmed) {
        echo "СБП-платёж подтверждён\n";
    }

    if ($state->qrCancelMessage !== null) {
        echo "QR cancel: {$state->qrCancelMessage}\n";
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
