<?php

declare(strict_types=1);

/**
 * Пример: Init с данными маркетплейса (Shops) и комиссией.
 *
 * Запуск:
 *   TBANK_TERMINAL_KEY=... TBANK_PASSWORD=... php examples/17-marketplace-shops.php
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\DTO\Shared\ShopDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

$request = new InitPaymentRequestDto(
    amount     : 150_000,
    orderId    : uniqueOrderId('mp'),
    description: 'Заказ маркетплейса',
    shops      : [
        new ShopDto(
            shopCode: '10001',
            amount  : 100_000,
            name    : 'Футболка синяя',
            fee     : 2500,
        ),
        new ShopDto(
            shopCode: '10002',
            amount  : 50_000,
            name    : 'Доставка',
        ),
    ],
);

try {
    $response = $client->payment()->init($request);

    echo "PaymentId: {$response->paymentId}\n";
    echo "Status:    {$response->status->value}\n";

    if ($response->hasPaymentUrl()) {
        echo "PaymentURL: {$response->paymentUrl}\n";
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
