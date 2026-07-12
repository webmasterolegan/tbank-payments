<?php

declare(strict_types=1);

/**
 * Пример: списание по RebillId (Charge).
 *
 * Сценарий:
 *   1. Init с Recurrent=true → покупатель оплачивает и привязывает карту.
 *   2. RebillId приходит в webhook.
 *   3. Init нового платежа → Charge с RebillId.
 *
 * Запуск:
 *   php examples/10-charge.php <PaymentId> <RebillId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\ChargeRequestDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$paymentId = $argv[1] ?? null;
$rebillId  = $argv[2] ?? null;

if ($paymentId === null || $rebillId === null) {
    fwrite(STDERR, "Usage: php examples/10-charge.php <PaymentId> <RebillId>\n");
    exit(1);
}

$client = createClient();

try {
    $response = $client->payment()->charge(
        new ChargeRequestDto(
            paymentId: $paymentId,
            rebillId : $rebillId,
        ),
    );

    echo "Charge OK\n";
    echo "Status:  {$response->status->value}\n";
    echo "Amount:  {$response->amount} коп.\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
