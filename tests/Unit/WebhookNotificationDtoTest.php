<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\WebhookNotificationDto;
use TBank\Payments\Enum\{NotificationTypeEnum, PaymentStatusEnum};

final class WebhookNotificationDtoTest extends TestCase
{
    public function testRawRedactedRemovesCardData(): void
    {
        $notification = WebhookNotificationDto::fromArray([
            'TerminalKey' => 'TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'CONFIRMED',
            'Amount'      => '10000',
            'Success'     => 'true',
            'Pan'         => '430000******0777',
            'ExpDate'     => '1225',
            'CardId'      => 'card-1',
        ]);

        $redacted = $notification->rawRedacted();

        $this->assertSame(PaymentStatusEnum::Confirmed, $notification->status);
        $this->assertArrayNotHasKey('Pan', $redacted);
        $this->assertArrayNotHasKey('ExpDate', $redacted);
        $this->assertArrayNotHasKey('CardId', $redacted);
        $this->assertSame('order-1', $redacted['OrderId']);
    }

    public function testRawRedactedRemovesAccountToken(): void
    {
        $notification = WebhookNotificationDto::fromArray([
            'TerminalKey'      => 'TERM',
            'OrderId'          => 'order-1',
            'PaymentId'        => '111',
            'Status'           => 'ACTIVE',
            'Amount'           => '0',
            'Success'          => 'true',
            'NotificationType' => 'QR',
            'AccountToken'     => 'secret-token',
            'RequestKey'       => 'req-key-1',
        ]);

        $this->assertSame(NotificationTypeEnum::Qr, $notification->notificationType);
        $this->assertSame('secret-token', $notification->accountToken);
        $this->assertSame('req-key-1', $notification->requestKey);
        $this->assertArrayNotHasKey('AccountToken', $notification->rawRedacted());
    }

    public function testWithoutSensitiveDataClearsFields(): void
    {
        $notification = WebhookNotificationDto::fromArray([
            'TerminalKey'  => 'TERM',
            'OrderId'      => 'order-1',
            'PaymentId'    => '111',
            'Status'       => 'CONFIRMED',
            'Amount'       => '10000',
            'Success'      => 'true',
            'Pan'          => '430000******0777',
            'AccountToken' => 'secret-token',
        ]);

        $safe = $notification->withoutSensitiveData();

        $this->assertNotSame($notification, $safe);
        $this->assertNull($safe->pan);
        $this->assertNull($safe->accountToken);
        $this->assertArrayNotHasKey('Pan', $safe->raw);
        $this->assertSame('order-1', $safe->orderId);
    }
}
