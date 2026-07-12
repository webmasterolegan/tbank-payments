<?php

declare(strict_types=1);

/**
 * Пример: регистрация и получение покупателя.
 *
 * Запуск:
 *   php examples/12-customer.php add <CustomerKey>
 *   php examples/12-customer.php get <CustomerKey>
 *   php examples/12-customer.php remove <CustomerKey>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\AddCustomerRequestDto;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$action      = $argv[1] ?? 'get';
$customerKey = $argv[2] ?? 'user-42';

$client = createClient();

try {
    if ($action === 'add') {
        $response = $client->customer()->add(
            new AddCustomerRequestDto(
                customerKey: $customerKey,
                email      : 'user@example.com',
                phone      : '+79001234567',
            ),
        );

        echo $response->success
            ? "Customer {$response->customerKey} registered\n"
            : "Failed: {$response->message}\n";
    } elseif ($action === 'remove') {
        $response = $client->customer()->remove($customerKey);

        echo $response->success
            ? "Customer {$response->customerKey} removed\n"
            : "Failed: {$response->message}\n";
    } else {
        $response = $client->customer()->get($customerKey);

        echo "CustomerKey: {$response->customerKey}\n";
        echo "Email:       {$response->email}\n";
        echo "Phone:       {$response->phone}\n";
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
