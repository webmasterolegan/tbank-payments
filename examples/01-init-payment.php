<?php

declare(strict_types=1);

/**
 * Пример: одностадийный платёж с чеком (Init).
 *
 * Запуск:
 *   TBANK_TERMINAL_KEY=... TBANK_PASSWORD=... php examples/01-init-payment.php
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\DTO\Shared\{ReceiptDto, ReceiptItemDto};
use TBank\Payments\Enum\{LanguageEnum, PayTypeEnum};
use TBank\Payments\Enum\Fiscal\{TaxationEnum, VatEnum};
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

$request = new InitPaymentRequestDto(
    amount         : 150_000,
    orderId        : uniqueOrderId(),
    description    : 'Заказ из примера SDK',
    payType        : PayTypeEnum::OneStep,
    language       : LanguageEnum::Ru,
    notificationUrl: 'https://myshop.ru/api/tbank/webhook',
    successUrl     : 'https://myshop.ru/payment/success',
    failUrl        : 'https://myshop.ru/payment/fail',
    receipt        : new ReceiptDto(
        taxation: TaxationEnum::UsnIncome,
        email   : 'buyer@example.com',
        items   : [
            new ReceiptItemDto(
                name    : 'Футболка синяя',
                price   : 150_000,
                quantity: 1.0,
                amount  : 150_000,
                tax     : VatEnum::None,
            ),
        ],
    ),
    data: ['order_number' => '2024-001'],
);

try {
    $response = $client->payment()->init($request);

    echo "PaymentId: {$response->paymentId}\n";
    echo "Status:    {$response->status->value}\n";

    if ($response->hasPaymentUrl()) {
        echo "PaymentURL: {$response->paymentUrl}\n";
        // header('Location: ' . $response->paymentUrl);
    }
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
