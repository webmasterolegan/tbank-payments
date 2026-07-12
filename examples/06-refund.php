<?php

declare(strict_types=1);

/**
 * Пример: полный и частичный возврат (Cancel).
 *
 * Запуск:
 *   php examples/06-refund.php full <PaymentId>
 *   php examples/06-refund.php partial <PaymentId> <AmountInKopecks>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\CancelRequestDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$mode      = $argv[1] ?? null;
$paymentId = $argv[2] ?? null;
$amount    = isset($argv[3]) ? (int) $argv[3] : null;

if ($paymentId === null || !in_array($mode, ['full', 'partial'], true)) {
    fwrite(STDERR, "Usage:\n");
    fwrite(STDERR, "  php examples/06-refund.php full <PaymentId>\n");
    fwrite(STDERR, "  php examples/06-refund.php partial <PaymentId> <AmountInKopecks>\n");
    exit(1);
}

$client = createClient();

try {
    $response = $client->refund()->cancel(
        new CancelRequestDto(
            paymentId: $paymentId,
            amount     : $mode === 'partial' ? $amount : null,
        ),
    );

    echo "Refund OK\n";
    echo "Status:       {$response->status->value}\n";
    echo "Original:     {$response->origAmount} коп.\n";
    echo "New amount:   {$response->newAmount} коп.\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
