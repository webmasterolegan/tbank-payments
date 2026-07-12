<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\ResendRequestDto;
use TBank\Payments\Enum\NotificationTypeEnum;

$paymentId = $argv[1] ?? null;

$response = $client->notifications()->resend(
    $paymentId !== null
        ? new ResendRequestDto(
            paymentId       : $paymentId,
            notificationType: NotificationTypeEnum::Payment,
        )
        : null,
);

echo "Resent notifications: {$response->count}\n";
