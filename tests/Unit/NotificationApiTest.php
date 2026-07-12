<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\ResendRequestDto;
use TBank\Payments\Enum\NotificationTypeEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class NotificationApiTest extends TestCase
{
    public function testResendWithoutParams(): void
    {
        $http = new FakeHttpClient([
            'Success' => true,
            'Count'   => 3,
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->notifications()->resend();

        $this->assertSame('Resend', $http->requests[0]['endpoint']);
        $this->assertArrayNotHasKey('PaymentId', $http->requests[0]['payload']);
        $this->assertArrayNotHasKey('NotificationTypes', $http->requests[0]['payload']);
        $this->assertSame(3, $response->count);
    }

    public function testResendWithPaymentIdAndNotificationType(): void
    {
        $http = new FakeHttpClient([
            'Success' => true,
            'Count'   => 1,
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->notifications()->resend(
            new ResendRequestDto(
                paymentId       : '555',
                notificationType: NotificationTypeEnum::Payment,
            ),
        );

        $this->assertSame('555', $http->requests[0]['payload']['PaymentId']);
        $this->assertSame('PAYMENT', $http->requests[0]['payload']['NotificationTypes']);
        $this->assertSame(1, $response->count);
    }
}
