<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Support\ApiValueParser;

final class ApiValueParserTest extends TestCase
{
    public function testParseSuccessFromBoolean(): void
    {
        $this->assertTrue(ApiValueParser::parseSuccess(true));
        $this->assertFalse(ApiValueParser::parseSuccess(false));
    }

    public function testParseSuccessFromString(): void
    {
        $this->assertTrue(ApiValueParser::parseSuccess('true'));
        $this->assertTrue(ApiValueParser::parseSuccess('TRUE'));
        $this->assertTrue(ApiValueParser::parseSuccess('1'));
        $this->assertFalse(ApiValueParser::parseSuccess('false'));
        $this->assertFalse(ApiValueParser::parseSuccess('FALSE'));
        $this->assertFalse(ApiValueParser::parseSuccess('0'));
    }

    public function testAsStringAndAsInt(): void
    {
        $this->assertSame('hello', ApiValueParser::asString('hello'));
        $this->assertSame('42', ApiValueParser::asString(42));
        $this->assertSame('default', ApiValueParser::asString(['array'], 'default'));
        $this->assertSame(19200, ApiValueParser::asInt(19200));
        $this->assertSame(100, ApiValueParser::asInt('100'));
        $this->assertSame(0, ApiValueParser::asInt('invalid'));
    }

    public function testAsNullableString(): void
    {
        $this->assertNull(ApiValueParser::asNullableString(null));
        $this->assertSame('err', ApiValueParser::asNullableString('err'));
        $this->assertNull(ApiValueParser::asNullableString(['x']));
    }

    public function testAsPaymentStatus(): void
    {
        $this->assertSame(PaymentStatusEnum::Confirmed, ApiValueParser::asPaymentStatus('CONFIRMED'));
        $this->assertSame(PaymentStatusEnum::Unknown, ApiValueParser::asPaymentStatus('NEW_STATUS'));
    }
}
