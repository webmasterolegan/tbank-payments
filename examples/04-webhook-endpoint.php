<?php

declare(strict_types=1);

/**
 * Пример: обработчик webhook (NotificationURL).
 *
 * Разместите этот скрипт на публичном HTTPS-эндпоинте, например:
 *   https://myshop.ru/api/tbank/webhook
 *
 * T-Bank ожидает HTTP 200 и тело "OK" (без кавычек).
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Exceptions\{InvalidWebhookPayloadException, InvalidWebhookSignatureException};
use TBank\Payments\TokenGenerator;
use TBank\Payments\WebhookHandler;

$terminalKey = getenv('TBANK_TERMINAL_KEY') ?: 'YOUR_TERMINAL_KEY';
$password    = getenv('TBANK_PASSWORD') ?: 'YOUR_PASSWORD';

$handler = new WebhookHandler(
    tokenGenerator     : new TokenGenerator($password),
    expectedTerminalKey: $terminalKey,
);

try {
    $notification = $handler->handle(file_get_contents('php://input') ?: '');

    if (!$notification->success) {
        // Платёж не прошёл — обновите статус заказа
        http_response_code(200);
        echo $handler->acknowledge();
        exit;
    }

    match ($notification->status) {
        PaymentStatusEnum::Confirmed => markOrderPaid(
            orderId  : $notification->orderId,
            paymentId: $notification->paymentId,
            amount   : $notification->amount,
        ),
        PaymentStatusEnum::Authorized => markOrderAuthorized(
            orderId  : $notification->orderId,
            paymentId: $notification->paymentId,
        ),
        PaymentStatusEnum::Rejected => markOrderRejected(
            orderId: $notification->orderId,
        ),
        PaymentStatusEnum::Refunded,
        PaymentStatusEnum::PartialRefunded => markOrderRefunded(
            orderId: $notification->orderId,
            amount : $notification->amount,
        ),
        PaymentStatusEnum::Unknown => logUnknownStatus($notification),
        default => null,
    };

    http_response_code(200);
    echo $handler->acknowledge();
} catch (InvalidWebhookSignatureException) {
    http_response_code(400);
    echo 'Bad signature';
} catch (InvalidWebhookPayloadException) {
    http_response_code(400);
    echo 'Bad payload';
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo 'Error';
}

// --- заглушки бизнес-логики ---

function markOrderPaid(string $orderId, string $paymentId, int $amount): void
{
    error_log("Order {$orderId} paid: {$paymentId}, amount={$amount}");
}

function markOrderAuthorized(string $orderId, string $paymentId): void
{
    error_log("Order {$orderId} authorized: {$paymentId}");
}

function markOrderRejected(string $orderId): void
{
    error_log("Order {$orderId} rejected");
}

function markOrderRefunded(string $orderId, int $amount): void
{
    error_log("Order {$orderId} refunded, amount={$amount}");
}

function logUnknownStatus(object $notification): void
{
    error_log('Unknown payment status: ' . ($notification->raw['Status'] ?? ''));
}
