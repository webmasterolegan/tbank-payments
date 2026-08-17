<?php

declare(strict_types=1);

/**
 * Пример: повторная отправка уведомлений (Resend).
 *
 * Запуск:
 *   php examples/15-resend.php
 *   php examples/15-resend.php <PaymentId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\ResendRequestDto;
use TBank\Payments\Enum\NotificationTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$paymentId = $argv[1] ?? null;

$client = createClient();

try {
    $response = $client->notifications()->resend(
        $paymentId !== null
            ? new ResendRequestDto(
                paymentId       : $paymentId,
                notificationType: NotificationTypeEnum::Payment,
            )
            : null,
    );

    echo "Resent notifications: {$response->count}\n";
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}
