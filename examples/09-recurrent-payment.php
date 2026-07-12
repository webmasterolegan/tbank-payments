<?php

declare(strict_types=1);

/**
 * Пример: рекуррентный платёж по привязанной карте.
 *
 * Покупатель должен быть заранее привязан через AddCard.
 * RebillId приходит в webhook после успешной привязки.
 *
 * Запуск:
 *   php examples/09-recurrent-payment.php <CustomerKey>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$customerKey = $argv[1] ?? 'user-42';

$client = createClient();

try {
    $response = $client->payment()->init(
        new InitPaymentRequestDto(
            amount     : 99_900,
            orderId    : uniqueOrderId('sub'),
            description: 'Подписка на сервис',
            customerKey: $customerKey,
            recurrent  : true,
        ),
    );

    echo "Recurrent payment initiated\n";
    echo "PaymentId: {$response->paymentId}\n";
    echo "Status:    {$response->status->value}\n";

    // Для рекуррента PaymentURL может не потребоваться —
    // списание произойдёт автоматически по RebillId.
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
