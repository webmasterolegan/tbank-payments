<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Response\InitPaymentResponseDto;
use TBank\Payments\Enum\PaymentStatusEnum;

final class InitPaymentResponseDtoTest extends TestCase
{
    public function testFromArrayMapsAllFields(): void
    {
        $data = [
            'Success'     => true,
            'TerminalKey' => 'TERM',
            'Status'      => 'NEW',
            'PaymentId'   => '12345',
            'OrderId'     => 'order-001',
            'Amount'      => 19200,
            'PaymentURL'  => 'https://securepay.tinkoff.ru/new/pay/...',
        ];

        $response = InitPaymentResponseDto::fromArray($data);

        $this->assertTrue($response->success);
        $this->assertSame('TERM', $response->terminalKey);
        $this->assertSame(PaymentStatusEnum::New, $response->status);
        $this->assertSame('12345', $response->paymentId);
        $this->assertSame('order-001', $response->orderId);
        $this->assertSame(19200, $response->amount);
        $this->assertTrue($response->hasPaymentUrl());
    }

    public function testInitMapsUnknownStatusToUnknownEnum(): void
    {
        $response = InitPaymentResponseDto::fromArray([
            'Success'   => true,
            'Status'    => 'FUTURE_STATUS',
            'PaymentId' => '1',
            'OrderId'   => 'o',
            'Amount'    => 100,
        ]);

        $this->assertSame(PaymentStatusEnum::Unknown, $response->status);
    }

    public function testHasPaymentUrlReturnsFalseWhenNull(): void
    {
        $response = InitPaymentResponseDto::fromArray([
            'Success'   => false,
            'Status'    => 'REJECTED',
            'PaymentId' => '',
            'OrderId'   => '',
            'Amount'    => 0,
        ]);

        $this->assertFalse($response->hasPaymentUrl());
        $this->assertSame(PaymentStatusEnum::Rejected, $response->status);
    }
}
