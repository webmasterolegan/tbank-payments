<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\NotificationTypeEnum;

final class NotificationTypeEnumTest extends TestCase
{
    public function testDefaultsToPaymentWhenMissing(): void
    {
        $this->assertSame(NotificationTypeEnum::Payment, NotificationTypeEnum::fromPayload(null));
        $this->assertSame(NotificationTypeEnum::Payment, NotificationTypeEnum::fromPayload(''));
    }

    public function testParsesKnownTypes(): void
    {
        $this->assertSame(NotificationTypeEnum::LinkCard, NotificationTypeEnum::fromPayload('LINKCARD'));
        $this->assertSame(NotificationTypeEnum::Fiscalization, NotificationTypeEnum::fromPayload('fiscalization'));
    }

    public function testUnknownType(): void
    {
        $this->assertSame(NotificationTypeEnum::Unknown, NotificationTypeEnum::fromPayload('SOMETHING_NEW'));
    }
}
