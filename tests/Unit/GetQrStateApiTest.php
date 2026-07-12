<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class GetQrStateApiTest extends TestCase
{
    public function testGetQrStateParsesResponse(): void
    {
        $http = new FakeHttpClient([
            'Success'         => true,
            'Status'          => 'CONFIRMED',
            'PaymentId'       => '700031849',
            'OrderId'         => '7830122',
            'Amount'          => 10000,
            'QrCancelCode'    => 'I05043',
            'QrCancelMessage' => 'Нет расчетного счета',
            'Message'         => 'OK',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->getQrState('700031849');

        $this->assertSame('GetQrState', $http->requests[0]['endpoint']);
        $this->assertSame('700031849', $http->requests[0]['payload']['PaymentId']);
        $this->assertSame(PaymentStatusEnum::Confirmed, $response->status);
        $this->assertSame('I05043', $response->qrCancelCode);
    }
}
