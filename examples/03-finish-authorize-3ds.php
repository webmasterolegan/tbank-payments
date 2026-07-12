<?php

declare(strict_types=1);

/**
 * Пример: FinishAuthorize после 3DS.
 *
 * Используется при собственной платёжной форме (не редирект на T-Bank).
 * После возврата покупателя из ACS передайте MD и PaRes.
 *
 * Запуск:
 *   php examples/03-finish-authorize-3ds.php <PaymentId> <MD> <PaRes>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\FinishAuthorizeRequestDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$paymentId = $argv[1] ?? null;
$md        = $argv[2] ?? null;
$paRes     = $argv[3] ?? null;

if ($paymentId === null || $md === null || $paRes === null) {
    fwrite(STDERR, "Usage: php examples/03-finish-authorize-3ds.php <PaymentId> <MD> <PaRes>\n");
    exit(1);
}

$client = createClient();

try {
    $response = $client->payment()->finishAuthorize(
        new FinishAuthorizeRequestDto(
            paymentId: $paymentId,
            md       : $md,
            paRes    : $paRes,
        ),
    );

    echo "Success: {$response->success}\n";
    echo "Status:  {$response->status->value}\n";

    if ($response->requires3ds()) {
        echo "Требуется 3DS, ACS URL: {$response->acsUrl}\n";
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
