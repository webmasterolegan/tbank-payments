<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\PaymentStatusEnum;

final class PaymentStatusEnumTest extends TestCase
{
    public function testConfirmedIsSuccessful(): void
    {
        $this->assertTrue(PaymentStatusEnum::Confirmed->isSuccessful());
    }

    public function testNewIsNotSuccessful(): void
    {
        $this->assertFalse(PaymentStatusEnum::New->isSuccessful());
    }

    public function testFinalStatuses(): void
    {
        $this->assertTrue(PaymentStatusEnum::Confirmed->isFinal());
        $this->assertTrue(PaymentStatusEnum::Rejected->isFinal());
        $this->assertTrue(PaymentStatusEnum::Reversed->isFinal());
        $this->assertTrue(PaymentStatusEnum::Refunded->isFinal());
        $this->assertFalse(PaymentStatusEnum::New->isFinal());
        $this->assertFalse(PaymentStatusEnum::Authorized->isFinal());
    }

    public function testFromValue(): void
    {
        $this->assertSame(PaymentStatusEnum::Confirmed, PaymentStatusEnum::from('CONFIRMED'));
        $this->assertSame(PaymentStatusEnum::New, PaymentStatusEnum::from('NEW'));
    }

    public function testUnknownIsNotSuccessfulOrFinal(): void
    {
        $this->assertFalse(PaymentStatusEnum::Unknown->isSuccessful());
        $this->assertFalse(PaymentStatusEnum::Unknown->isFinal());
    }
}
