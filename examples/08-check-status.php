<?php

declare(strict_types=1);

/**
 * Пример: проверка статуса платежа и заказа.
 *
 * Запуск:
 *   php examples/08-check-status.php payment <PaymentId>
 *   php examples/08-check-status.php order <OrderId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\Exceptions\{ApiException, NetworkException};

$mode = $argv[1] ?? null;
$id   = $argv[2] ?? null;

if ($id === null || !in_array($mode, ['payment', 'order'], true)) {
    fwrite(STDERR, "Usage:\n");
    fwrite(STDERR, "  php examples/08-check-status.php payment <PaymentId>\n");
    fwrite(STDERR, "  php examples/08-check-status.php order <OrderId>\n");
    exit(1);
}

$client = createClient();

try {
    if ($mode === 'payment') {
        $state = $client->status()->getState($id);

        echo "PaymentId: {$state->paymentId}\n";
        echo "OrderId:   {$state->orderId}\n";
        echo "Status:    {$state->status->value}\n";
        echo "Amount:    {$state->amount} коп.\n";
        echo "Paid:      " . ($state->isPaid() ? 'yes' : 'no') . "\n";
    } else {
        $order = $client->status()->checkOrder($id);

        echo "OrderId: {$order->orderId}\n";
        echo "Payments: " . count($order->payments) . "\n";

        foreach ($order->payments as $payment) {
            echo "  - {$payment->paymentId}: {$payment->status->value}, {$payment->amount} коп.\n";
        }
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
