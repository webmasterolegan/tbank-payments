<?php

declare(strict_types=1);

/**
 * Пример: двухстадийный платёж (Init → Confirm).
 *
 * 1. Init с PayType::TwoStep — холдирование средств.
 * 2. Confirm — списание после отгрузки товара.
 *
 * Запуск:
 *   TBANK_TERMINAL_KEY=... TBANK_PASSWORD=... php examples/02-two-step-payment.php
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\{ConfirmRequestDto, InitPaymentRequestDto};
use TBank\Payments\Enum\PayTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

try {
    // Шаг 1: инициация с холдом
    $init = $client->payment()->init(
        new InitPaymentRequestDto(
            amount     : 50_000,
            orderId    : uniqueOrderId('hold'),
            description: 'Двухстадийный платёж',
            payType    : PayTypeEnum::TwoStep,
            successUrl : 'https://myshop.ru/success',
            failUrl    : 'https://myshop.ru/fail',
        ),
    );

    echo "Init OK\n";
    echo "PaymentId: {$init->paymentId}\n";
    echo "Status:    {$init->status->value}\n";

    if ($init->hasPaymentUrl()) {
        echo "Перенаправьте покупателя: {$init->paymentUrl}\n";
        // После оплаты статус станет AUTHORIZED (через webhook или GetState).
        exit(0);
    }

    // Шаг 2: подтверждение списания (когда товар отгружен)
    $paymentId = $init->paymentId;

    $confirm = $client->payment()->confirm(
        new ConfirmRequestDto(paymentId: $paymentId),
    );

    echo "Confirm OK\n";
    echo "Status: {$confirm->status->value}\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
