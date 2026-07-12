<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\{AddAccountQrRequestDto, ChargeQrRequestDto, InitPaymentRequestDto};
use TBank\Payments\Enum\QrDataTypeEnum;

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

// 3. Автоплатёж (после получения AccountToken из webhook NotificationType=QR)
$accountToken = getenv('TBANK_ACCOUNT_TOKEN') ?: 'account-token-from-webhook';

$init = $client->payment()->init(
    new InitPaymentRequestDto(
        amount     : 10000,
        orderId    : 'sbp-autopay-' . time(),
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
