<?php

declare(strict_types=1);

/**
 * Пример: закрывающий чек (SendClosingReceipt).
 *
 * Используется для двухстадийных платежей и предоплаты —
 * чек пробивается после фактической отгрузки.
 *
 * Запуск:
 *   php examples/07-receipt.php <PaymentId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\SendReceiptRequestDto;
use TBank\Payments\DTO\Shared\{ReceiptDto, ReceiptItemDto};
use TBank\Payments\Enum\Fiscal\{PaymentMethodEnum, PaymentObjectEnum, TaxationEnum, VatEnum};
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$paymentId = $argv[1] ?? null;

if ($paymentId === null) {
    fwrite(STDERR, "Usage: php examples/07-receipt.php <PaymentId>\n");
    exit(1);
}

$client = createClient();

try {
    $response = $client->receipt()->sendClosingReceipt(
        new SendReceiptRequestDto(
            paymentId: $paymentId,
            receipt  : new ReceiptDto(
                taxation: TaxationEnum::UsnIncome,
                email   : 'buyer@example.com',
                items   : [
                    new ReceiptItemDto(
                        name         : 'Футболка синяя',
                        price        : 150_000,
                        quantity     : 1.0,
                        amount       : 150_000,
                        tax          : VatEnum::None,
                        paymentObject: PaymentObjectEnum::Commodity,
                        paymentMethod: PaymentMethodEnum::FullPayment,
                    ),
                ],
            ),
        ),
    );

    echo $response->success ? "Receipt sent\n" : "Receipt failed: {$response->message}\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
