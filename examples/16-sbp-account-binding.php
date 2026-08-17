<?php

declare(strict_types=1);

/**
 * Пример: привязка счёта СБП и автоплатёж (AddAccountQr + ChargeQr).
 *
 * Запуск:
 *   php examples/16-sbp-account-binding.php
 *
 * AccountToken приходит в webhook (NotificationType=QR) после привязки.
 * Для шага ChargeQr задайте TBANK_ACCOUNT_TOKEN.
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\{AddAccountQrRequestDto, ChargeQrRequestDto, InitPaymentRequestDto};
use TBank\Payments\Enum\QrDataTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$client = createClient();

try {
    // 1. Привязка счёта — показать QR покупателю
    $binding = $client->sbp()->addAccountQr(
        new AddAccountQrRequestDto(
            description: 'Привязка счёта для автоплатежей',
            dataType   : QrDataTypeEnum::Payload,
        ),
    );

    echo "RequestKey: {$binding->requestKey}\n";
    echo "QR payload: {$binding->data}\n";

    // 2. Проверить статус привязки (или дождаться webhook с AccountToken)
    $state = $client->sbp()->getAddAccountQrState($binding->requestKey);

    if ($state->status->isBound()) {
        echo "Счёт привязан: {$state->bankMemberName}\n";
    }

    $accountToken = getenv('TBANK_ACCOUNT_TOKEN') ?: '';

    if ($accountToken === '') {
        echo "Задайте TBANK_ACCOUNT_TOKEN для шага ChargeQr\n";
        exit(0);
    }

    // 3. Автоплатёж (после получения AccountToken из webhook NotificationType=QR)
    $init = $client->payment()->init(
        new InitPaymentRequestDto(
            amount     : 10000,
            orderId    : uniqueOrderId('sbp-autopay'),
            description: 'Автоплатёж СБП',
            recurrent  : true,
            data       : ['QR' => 'true'],
        ),
    );

    $charge = $client->sbp()->chargeQr(
        new ChargeQrRequestDto(
            paymentId   : $init->paymentId,
            accountToken: $accountToken,
        ),
    );

    echo "Charge status: {$charge->status->value}\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
