<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;
use TBank\Payments\TokenGenerator;

final class PaymentApiTest extends TestCase
{
    public function testInitSignsRequestAndParsesResponse(): void
    {
        $http = new FakeHttpClient([
            'Success'     => true,
            'TerminalKey' => 'TERM',
            'Status'      => 'NEW',
            'PaymentId'   => '999',
            'OrderId'     => 'order-1',
            'Amount'      => 10000,
            'PaymentURL'  => 'https://securepay.tinkoff.ru/pay',
        ]);

        $client = new TBankClient(
            terminalKey: 'TERM',
            password   : 'secret',
            httpClient : $http,
        );

        $response = $client->payment()->init(
            new InitPaymentRequestDto(amount: 10000, orderId: 'order-1'),
        );

        $this->assertCount(1, $http->requests);
        $this->assertSame('Init', $http->requests[0]['endpoint']);

        $payload = $http->requests[0]['payload'];
        $this->assertSame('TERM', $payload['TerminalKey']);
        $this->assertSame(10000, $payload['Amount']);
        $this->assertArrayHasKey('Token', $payload);

        $expectedToken = (new TokenGenerator('secret'))->generate([
            'Amount'      => 10000,
            'OrderId'     => 'order-1',
            'TerminalKey' => 'TERM',
        ]);
        $this->assertSame($expectedToken, $payload['Token']);

        $this->assertSame(PaymentStatusEnum::New, $response->status);
        $this->assertSame('999', $response->paymentId);
        $this->assertTrue($response->hasPaymentUrl());
    }

    public function testInitMapsUnknownStatusToUnknownEnum(): void
    {
        $http = new FakeHttpClient([
            'Success'   => true,
            'Status'    => 'FUTURE_STATUS',
            'PaymentId' => '1',
            'OrderId'   => 'o',
            'Amount'    => 100,
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->payment()->init(
            new InitPaymentRequestDto(amount: 100, orderId: 'o'),
        );

        $this->assertSame(PaymentStatusEnum::Unknown, $response->status);
    }
}
