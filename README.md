# tbank-payments

PHP 8.5+ SDK для работы с API интернет-эквайринга **T-Bank** (бывший Tinkoff).

[![PHP 8.5+](https://img.shields.io/badge/PHP-8.5%2B-blue.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## Возможности

| Группа | Методы |
|---|---|
| Платежи | Init, FinishAuthorize, Confirm, Charge |
| СБП | GetQr, GetQrState, GetQrBankList, QrMembersList, ChargeQr, AddAccountQr, GetAddAccountQrState |
| Статус | GetState, CheckOrder |
| Уведомления | Resend |
| Отмена / возврат | Cancel (полный и частичный) |
| Карты | AddCard, GetCardList, RemoveCard |
| Покупатели | AddCustomer, GetCustomer, RemoveCustomer |
| Чеки (ФФД 1.2) | SendClosingReceipt |
| Webhook | Валидация подписи, типизированное уведомление |

---

## Установка

```bash
composer require webmasterolegan/tbank-payments
```

**Требования:** PHP ≥ 8.5, расширения `curl`, `json`, `uri`.

---

## Быстрый старт

```php
use TBank\Payments\Enum\EnvironmentEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\DTO\Shared\{ReceiptDto, ReceiptItemDto};
use TBank\Payments\Enum\Fiscal\{TaxationEnum, VatEnum};

$client = new TBankClient(
    terminalKey: 'YOUR_TERMINAL_KEY',
    password   : 'YOUR_PASSWORD',
    environment: EnvironmentEnum::Production,
);
```

### 1. Инициировать платёж

```php
use TBank\Payments\Enum\{LanguageEnum, PayTypeEnum};

$request = new InitPaymentRequestDto(
    amount     : 150000,
    orderId    : 'order-2024-001',
    description: 'Заказ #2024-001',
    payType    : PayTypeEnum::OneStep,
    language   : LanguageEnum::Ru,
    successUrl : 'https://myshop.ru/success',
    failUrl    : 'https://myshop.ru/fail',
    receipt    : new ReceiptDto(
        taxation: TaxationEnum::UsnIncome,
        email   : 'buyer@example.com',
        items   : [
            new ReceiptItemDto(
                name    : 'Футболка синяя',
                price   : 150000,
                quantity: 1.0,
                amount  : 150000,
                tax     : VatEnum::None,
            ),
        ],
    ),
);

$response = $client->payment()->init($request);

if ($response->hasPaymentUrl()) {
    header('Location: ' . $response->paymentUrl);
    exit;
}
```

### 2. Подтвердить двухстадийное списание

```php
use TBank\Payments\DTO\Request\ConfirmRequestDto;

$confirm = $client->payment()->confirm(
    new ConfirmRequestDto(paymentId: '123456789')
);
```

### 3. Отменить / вернуть платёж

```php
use TBank\Payments\DTO\Request\CancelRequestDto;

$cancel = $client->refund()->cancel(
    new CancelRequestDto(paymentId: '123456789')
);

$cancel = $client->refund()->cancel(
    new CancelRequestDto(paymentId: '123456789', amount: 50000)
);
```

### 4. Получить статус платежа

```php
use TBank\Payments\Enum\PaymentStatusEnum;

$state = $client->status()->getState('123456789');

if ($state->status === PaymentStatusEnum::Confirmed) {
    // Платёж подтверждён
}

if ($state->status->isSuccessful()) {
    // то же через метод enum
}
```

### 5. Привязать карту

```php
use TBank\Payments\DTO\Request\AddCardRequestDto;
use TBank\Payments\Enum\CardCheckTypeEnum;

$result = $client->card()->addCard(
    new AddCardRequestDto(
        customerKey: 'user-42',
        checkType  : CardCheckTypeEnum::Hold,
    )
);

header('Location: ' . $result->paymentUrl);
```

### 6. Оплата по привязанной карте (рекуррент)

```php
$request = new InitPaymentRequestDto(
    amount     : 99900,
    orderId    : 'sub-2024-05',
    customerKey: 'user-42',
    recurrent  : true,
);

$init = $client->payment()->init($request);
```

### 7. Обработка webhook

```php
use TBank\Payments\Enum\{NotificationTypeEnum, PaymentStatusEnum};
use TBank\Payments\Exceptions\InvalidWebhookSignatureException;
use TBank\Payments\TBankClient;

$handler = $client->webhookHandler();

try {
    $notification = $handler->handle(file_get_contents('php://input'));

    if (!$notification->success) {
        http_response_code(200);
        echo $handler->acknowledge();
        exit;
    }

    match ($notification->notificationType) {
        NotificationTypeEnum::Payment      => handlePayment($notification),
        NotificationTypeEnum::LinkCard     => handleLinkCard($notification),
        NotificationTypeEnum::Fiscalization => handleFiscalization($notification),
        default                            => null,
    };

    match ($notification->status) {
        PaymentStatusEnum::Confirmed         => handleConfirmed($notification),
        PaymentStatusEnum::Rejected          => handleRejected($notification),
        PaymentStatusEnum::PartialRefunded   => handlePartialRefund($notification),
        PaymentStatusEnum::Unknown           => logUnknownStatus($notification),
        default                              => null,
    };

    http_response_code(200);
    echo $handler->acknowledge();
} catch (InvalidWebhookSignatureException) {
    http_response_code(400);
    echo 'Bad signature';
}
```

### 8. Список и удаление карт

```php
use TBank\Payments\DTO\Request\RemoveCardRequestDto;

$cards = $client->card()->getCardList('user-42');

foreach ($cards->cards as $card) {
    echo "{$card->cardId}: {$card->pan}\n";
}

$client->card()->removeCard(
    new RemoveCardRequestDto(customerKey: 'user-42', cardId: '123456'),
);
```

### 9. Статус заказа (несколько платежей)

```php
$order = $client->status()->checkOrder('order-2024-001');

foreach ($order->payments as $payment) {
    echo "{$payment->paymentId}: {$payment->status->value}\n";
}
```

### 10. Закрывающий чек

```php
use TBank\Payments\DTO\Request\SendReceiptRequestDto;
use TBank\Payments\Enum\Fiscal\{PaymentMethodEnum, PaymentObjectEnum};

$client->receipt()->sendClosingReceipt(
    new SendReceiptRequestDto(
        paymentId: '123456789',
        receipt  : new ReceiptDto(
            taxation: TaxationEnum::UsnIncome,
            email   : 'buyer@example.com',
            items   : [
                new ReceiptItemDto(
                    name         : 'Футболка синяя',
                    price        : 150000,
                    quantity     : 1.0,
                    amount       : 150000,
                    tax          : VatEnum::None,
                    paymentObject: PaymentObjectEnum::Commodity,
                    paymentMethod: PaymentMethodEnum::FullPayment,
                ),
            ],
        ),
    ),
);
```

### 11. FinishAuthorize (3DS, своя форма)

```php
use TBank\Payments\DTO\Request\FinishAuthorizeRequestDto;

$response = $client->payment()->finishAuthorize(
    new FinishAuthorizeRequestDto(
        paymentId: '123456789',
        md       : $_POST['MD'],
        paRes    : $_POST['PaRes'],
    ),
);

if ($response->requires3ds()) {
    // Редирект на ACS: $response->acsUrl
}
```

### 12. Тестовая среда

```php
$client = new TBankClient(
    terminalKey: 'YOUR_TERMINAL_KEY',
    password   : 'YOUR_PASSWORD',
    environment: EnvironmentEnum::Test,
);
```

### 13. Списание по RebillId (Charge)

```php
use TBank\Payments\DTO\Request\ChargeRequestDto;

$response = $client->payment()->charge(
    new ChargeRequestDto(
        paymentId: $init->paymentId,
        rebillId : $rebillIdFromWebhook,
    ),
);
```

### 14. Оплата через СБП (GetQr)

```php
use TBank\Payments\DTO\Request\GetQrRequestDto;
use TBank\Payments\Enum\QrDataTypeEnum;

$qr = $client->sbp()->getQr(
    new GetQrRequestDto(
        paymentId: $init->paymentId,
        dataType : QrDataTypeEnum::Payload,
    ),
);

echo $qr->data; // payload или SVG при QrDataTypeEnum::Image
```

### 15. Покупатели

```php
use TBank\Payments\DTO\Request\AddCustomerRequestDto;

$client->customer()->add(
    new AddCustomerRequestDto(
        customerKey: 'user-42',
        email      : 'user@example.com',
        phone      : '+79001234567',
    ),
);

$customer = $client->customer()->get('user-42');

$client->customer()->remove('user-42');
```

### 16. Статус СБП-платежа (GetQrState)

```php
$state = $client->sbp()->getQrState($paymentId);

if ($state->status === PaymentStatusEnum::Confirmed) {
    // СБП-платёж подтверждён
}
```

### 17. Привязка счёта СБП (AddAccountQr)

```php
use TBank\Payments\DTO\Request\AddAccountQrRequestDto;
use TBank\Payments\Enum\{AccountQrStatusEnum, NotificationTypeEnum, QrDataTypeEnum};

$binding = $client->sbp()->addAccountQr(
    new AddAccountQrRequestDto(
        description: 'Привязка счёта для автоплатежей',
        dataType   : QrDataTypeEnum::Payload,
    ),
);

// Показать QR: $binding->data
// Сохранить $binding->requestKey для проверки статуса

$state = $client->sbp()->getAddAccountQrState($binding->requestKey);

if ($state->status === AccountQrStatusEnum::Active) {
    // Счёт привязан; AccountToken придёт в webhook (NotificationType=QR)
}
```

В webhook-уведомлении типа `QR` доступны поля `accountToken` и `requestKey`:

```php
if ($notification->notificationType === NotificationTypeEnum::Qr) {
    saveAccountToken($notification->accountToken);
}
```

### 18. Список банков СБП (GetQrBankList)

```php
use TBank\Payments\DTO\Request\GetQrBankListRequestDto;
use TBank\Payments\DTO\Shared\DeviceDto;
use TBank\Payments\Enum\DeviceTypeEnum;

$banks = $client->sbp()->getQrBankList(
    new GetQrBankListRequestDto(
        device: new DeviceDto(DeviceTypeEnum::Mobile, 'Android'),
    ),
);

foreach ($banks->bankList as $bank) {
    echo "{$bank->bankName}: {$bank->nspkBankId}\n";
}
```

### 19. Автоплатёж СБП (ChargeQr)

```php
use TBank\Payments\DTO\Request\ChargeQrRequestDto;

$response = $client->sbp()->chargeQr(
    new ChargeQrRequestDto(
        paymentId   : $init->paymentId,
        accountToken: $accountTokenFromWebhook,
    ),
);
```

### 20. Повторная отправка уведомлений (Resend)

```php
use TBank\Payments\DTO\Request\ResendRequestDto;
use TBank\Payments\Enum\NotificationTypeEnum;

$result = $client->notifications()->resend(
    new ResendRequestDto(
        paymentId       : '123456789',
        notificationType: NotificationTypeEnum::Payment,
    ),
);

echo $result->count; // сколько уведомлений отправлено повторно
```

### 21. Повтор при сетевых ошибках и переиспользование cURL

```php
$client = new TBankClient(
    terminalKey      : 'YOUR_TERMINAL_KEY',
    password         : 'YOUR_PASSWORD',
    retryAttempts    : 3,    // до 3 попыток при NetworkException
    retryDelayMs     : 200,  // экспоненциальная задержка: 200, 400, 600 мс
    connectTimeout   : 10,   // таймаут установки соединения (сек)
    reuseConnection  : true, // persistent cURL share (FrankenPHP, RoadRunner)
);
```

### 22. PSR-18 HTTP-клиент с retry

По умолчанию SDK использует cURL. Для интеграции с Guzzle или Symfony HttpClient:

```php
use TBank\Payments\Http\{Psr18HttpClient, RetryingHttpClient};
use TBank\Payments\Enum\EnvironmentEnum;

$psr17 = new \Nyholm\Psr7\Factory\Psr17Factory();
$baseUrl = EnvironmentEnum::Production->baseUrl();

$client = new TBankClient(
    terminalKey: 'YOUR_TERMINAL_KEY',
    password   : 'YOUR_PASSWORD',
    environment: EnvironmentEnum::Production,
    httpClient : new RetryingHttpClient(
        inner       : new Psr18HttpClient(
            client        : $psr18Client,
            requestFactory: $psr17,
            streamFactory : $psr17,
            baseUrl       : $baseUrl,
        ),
        maxAttempts : 3,
        delayMs     : 200,
    ),
);
```

Альтернатива — встроенный retry через конструктор `TBankClient` (работает с любым `HttpClientContract`):

```php
$client = new TBankClient(
    terminalKey  : 'YOUR_TERMINAL_KEY',
    password     : 'YOUR_PASSWORD',
    retryAttempts: 3,
    retryDelayMs : 200,
);
```

Без обёртки — только cURL:

```php
use TBank\Payments\Http\Psr18HttpClient;
use TBank\Payments\Enum\EnvironmentEnum;

$psr17 = new \Nyholm\Psr7\Factory\Psr17Factory();

$client = new TBankClient(
    terminalKey: 'YOUR_TERMINAL_KEY',
    password   : 'YOUR_PASSWORD',
    environment: EnvironmentEnum::Production,
    httpClient : new Psr18HttpClient(
        client        : $psr18Client,      // Guzzle или Symfony HttpClient
        requestFactory: $psr17,
        streamFactory : $psr17,
        baseUrl       : EnvironmentEnum::Production->baseUrl(),
    ),
);
```

---

## Примеры (examples/)

В каталоге `examples/` — готовые скрипты. Перед запуском задайте переменные окружения:

```bash
export TBANK_TERMINAL_KEY=your_terminal_key
export TBANK_PASSWORD=your_password
export TBANK_ENV=production   # или test
```

| Скрипт | Описание |
|---|---|
| `01-init-payment.php` | Одностадийный платёж с чеком |
| `02-two-step-payment.php` | Двухстадийный платёж (холд + Confirm) |
| `03-finish-authorize-3ds.php` | Завершение 3DS (MD + PaRes) |
| `04-webhook-endpoint.php` | Обработчик NotificationURL |
| `05-cards.php` | Привязка, список, удаление карт |
| `06-refund.php` | Полный и частичный возврат |
| `07-receipt.php` | Закрывающий чек |
| `08-check-status.php` | Статус платежа и заказа |
| `09-recurrent-payment.php` | Рекуррентный платёж |
| `10-charge.php` | Списание по RebillId |
| `11-sbp-payment.php` | Init + GetQr (СБП) |
| `12-customer.php` | Регистрация и получение покупателя |
| `13-sbp-qr-state.php` | Статус СБП-платежа |
| `14-sbp-bank-list.php` | Список банков СБП |
| `15-resend.php` | Повторная отправка уведомлений |
| `16-sbp-account-binding.php` | Привязка счёта + автоплатёж СБП |

```bash
composer install
php examples/01-init-payment.php
php examples/05-cards.php list user-42
php examples/08-check-status.php payment 123456789
```

---

## Архитектура пакета

**Соглашения об именовании:** DTO — суффикс `Dto`, enum — `Enum`, интерфейсы — `Contract`.

```
src/
├── TBankClient.php
├── TokenGenerator.php
├── WebhookHandler.php
├── Enum/
│   ├── PaymentStatusEnum.php
│   ├── PayTypeEnum.php
│   ├── LanguageEnum.php
│   ├── CardCheckTypeEnum.php
│   ├── EnvironmentEnum.php
│   ├── Fiscal/              # TaxationEnum, VatEnum, PaymentObjectEnum, …
│   └── Card/                # CardStatusEnum, CardTypeEnum
├── Api/
├── DTO/
│   ├── Request/
│   ├── Response/
│   ├── Shared/
│   └── WebhookNotificationDto.php
├── Http/
│   ├── HttpClient.php
│   ├── HttpClientContract.php
│   ├── RetryingHttpClient.php
│   └── Psr18HttpClient.php
└── Exceptions/
    ├── TBankException.php
    ├── ApiException.php
    ├── NetworkException.php
    └── InvalidWebhookSignatureException.php
```

---

## Обработка ошибок

```php
use TBank\Payments\Exceptions\{ApiException, InvalidWebhookSignatureException, NetworkException, TBankException};

try {
    $response = $client->payment()->init($request);
} catch (ApiException $e) {
    echo $e->getMessage();
    echo $e->getErrorCode();
} catch (NetworkException $e) {
    echo $e->getMessage();
} catch (TBankException $e) {
    // прочие ошибки пакета
}
```

---

## Запуск тестов

```bash
composer install
composer ci      # тесты + PHPStan
composer test
```

---

## Ссылки

- [Документация T-Bank API](https://developer.tbank.ru/eacq/api/priem-platezhei)
- [Формирование токена](https://developer.tbank.ru/eacq/intro/developer/token)
- [Уведомления об операциях](https://developer.tbank.ru/eacq/intro/developer/notification)
- [Личный кабинет эквайринга](https://business.tbank.ru/oplata/main)

---

## Лицензия

MIT © Oleg Polyakov
