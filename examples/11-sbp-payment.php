<?php

declare(strict_types=1);

/**
 * Пример: оплата через СБП (Init + GetQr).
 *
 * Запуск:
 *   php examples/11-sbp-payment.php
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\{GetQrRequestDto, InitPaymentRequestDto};
use TBank\Payments\Enum\QrDataTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

try {
    $init = $client->payment()->init(
        new InitPaymentRequestDto(
            amount     : 50_000,
            orderId    : uniqueOrderId('sbp'),
            description: 'Оплата через СБП',
        ),
    );

    echo "PaymentId: {$init->paymentId}\n";

    $qr = $client->sbp()->getQr(
        new GetQrRequestDto(
            paymentId: $init->paymentId,
            dataType : QrDataTypeEnum::Payload,
        ),
    );

    if ($qr->hasQrData()) {
        echo "SBP payload: {$qr->data}\n";
        // Отобразите QR покупателю или перенаправьте на deeplink.
    } else {
        fwrite(STDERR, "QR not received: {$qr->message}\n");
        exit(1);
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
