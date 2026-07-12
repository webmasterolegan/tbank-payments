<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\ChargeRequestDto;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class ChargeApiTest extends TestCase
{
    public function testChargeSendsRebillIdAndParsesResponse(): void
    {
        $http = new FakeHttpClient([
            'Success'   => true,
            'Status'    => 'CONFIRMED',
            'PaymentId' => '555',
            'OrderId'   => 'order-1',
            'Amount'    => 99900,
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->payment()->charge(
            new ChargeRequestDto(
                paymentId: '555',
                rebillId : 'rebill-42',
            ),
        );

        $this->assertSame('Charge', $http->requests[0]['endpoint']);
        $this->assertSame('rebill-42', $http->requests[0]['payload']['RebillId']);
        $this->assertSame(PaymentStatusEnum::Confirmed, $response->status);
        $this->assertSame(99900, $response->amount);
    }
}
