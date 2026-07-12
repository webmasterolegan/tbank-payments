<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\AccountQrStatusEnum;

final class AccountQrStatusEnumTest extends TestCase
{
    public function testFromPayloadHandlesTypo(): void
    {
        $this->assertSame(
            AccountQrStatusEnum::Inactive,
            AccountQrStatusEnum::fromPayload('INACITVE'),
        );
    }

    public function testIsBoundOnlyForActive(): void
    {
        $this->assertTrue(AccountQrStatusEnum::Active->isBound());
        $this->assertFalse(AccountQrStatusEnum::Inactive->isBound());
    }
}
