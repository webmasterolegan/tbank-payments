<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\WebhookNotificationDto;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Exceptions\{InvalidWebhookPayloadException, InvalidWebhookSignatureException};
use TBank\Payments\TokenGenerator;
use TBank\Payments\WebhookHandler;

final class WebhookHandlerTest extends TestCase
{
    private WebhookHandler $handler;
    private TokenGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new TokenGenerator('secret');
        $this->handler   = new WebhookHandler($this->generator);
    }

    public function testValidSignatureReturnsNotification(): void
    {
        $payload = [
            'TerminalKey' => 'TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'CONFIRMED',
            'Amount'      => 10000,
            'Success'     => true,
        ];
        $payload['Token'] = $this->generator->generate($payload);

        $notification = $this->handler->handle($payload);

        $this->assertInstanceOf(WebhookNotificationDto::class, $notification);
        $this->assertSame(PaymentStatusEnum::Confirmed, $notification->status);
        $this->assertSame('order-1', $notification->orderId);
    }

    public function testInvalidSignatureThrowsException(): void
    {
        $this->expectException(InvalidWebhookSignatureException::class);

        (void) $this->handler->handle([
            'TerminalKey' => 'TERM',
            'Token'       => 'invalid-token',
            'Status'      => 'CONFIRMED',
        ]);
    }

    public function testHandleAcceptsJsonString(): void
    {
        $payload = [
            'TerminalKey' => 'TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'NEW',
            'Amount'      => 500,
            'Success'     => true,
        ];
        $payload['Token'] = $this->generator->generate($payload);

        $notification = $this->handler->handle(json_encode($payload));

        $this->assertSame(PaymentStatusEnum::New, $notification->status);
    }

    public function testAcknowledgeReturnsOK(): void
    {
        $this->assertSame('OK', $this->handler->acknowledge());
    }

    public function testStringSuccessFalseIsParsedCorrectly(): void
    {
        $payload = [
            'TerminalKey' => 'TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'REJECTED',
            'Amount'      => '10000',
            'Success'     => 'false',
        ];
        $payload['Token'] = $this->generator->generate($payload);

        $notification = $this->handler->handle($payload);

        $this->assertFalse($notification->success);
    }

    public function testUnexpectedTerminalKeyThrowsException(): void
    {
        $handler = new WebhookHandler($this->generator, 'EXPECTED');

        $payload = [
            'TerminalKey' => 'OTHER',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'CONFIRMED',
            'Amount'      => '10000',
            'Success'     => 'true',
        ];
        $payload['Token'] = $this->generator->generate($payload);

        $this->expectException(InvalidWebhookSignatureException::class);

        (void) $handler->handle($payload);
    }

    public function testMalformedJsonThrowsPayloadException(): void
    {
        $this->expectException(InvalidWebhookPayloadException::class);
        $this->expectExceptionMessage('malformed JSON');

        (void) $this->handler->handle('{not-json');
    }

    public function testUnknownStatusMapsToUnknownEnum(): void
    {
        $payload = [
            'TerminalKey' => 'TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'FUTURE_STATUS',
            'Amount'      => '10000',
            'Success'     => 'true',
        ];
        $payload['Token'] = $this->generator->generate($payload);

        $notification = $this->handler->handle($payload);

        $this->assertSame(PaymentStatusEnum::Unknown, $notification->status);
    }
}
