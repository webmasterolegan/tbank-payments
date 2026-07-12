<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\NotificationTypeEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;
use TBank\Payments\WebhookHandler;

final class TBankClientTest extends TestCase
{
    public function testWebhookHandlerUsesClientCredentials(): void
    {
        $client = new TBankClient(
            terminalKey: 'MY_TERM',
            password   : 'my-secret',
            httpClient : new FakeHttpClient(),
        );

        $handler = $client->webhookHandler();

        $this->assertInstanceOf(WebhookHandler::class, $handler);

        $payload = [
            'TerminalKey' => 'MY_TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'CONFIRMED',
            'Amount'      => '10000',
            'Success'     => 'true',
        ];

        $generator = new \TBank\Payments\TokenGenerator('my-secret');
        $payload['Token'] = $generator->generate($payload);

        $notification = $handler->handle($payload);

        $this->assertSame('order-1', $notification->orderId);
        $this->assertSame(NotificationTypeEnum::Payment, $notification->notificationType);
    }

    public function testWebhookHandlerRejectsWrongTerminalKey(): void
    {
        $client = new TBankClient('MY_TERM', 'secret', httpClient: new FakeHttpClient());
        $handler = $client->webhookHandler();

        $payload = [
            'TerminalKey' => 'OTHER_TERM',
            'OrderId'     => 'order-1',
            'PaymentId'   => '111',
            'Status'      => 'CONFIRMED',
            'Amount'      => '10000',
            'Success'     => 'true',
        ];
        $payload['Token'] = (new \TBank\Payments\TokenGenerator('secret'))->generate($payload);

        $this->expectException(\TBank\Payments\Exceptions\InvalidWebhookSignatureException::class);

        (void) $handler->handle($payload);
    }
}
