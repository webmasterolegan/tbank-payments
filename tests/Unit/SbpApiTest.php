<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\{
    AddAccountQrRequestDto,
    ChargeQrRequestDto,
    GetQrBankListRequestDto,
    GetQrRequestDto,
};
use TBank\Payments\DTO\Shared\DeviceDto;
use TBank\Payments\Enum\{
    AccountQrStatusEnum,
    DeviceTypeEnum,
    PaymentStatusEnum,
    QrDataTypeEnum,
    QrScenarioTypeEnum,
};
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class SbpApiTest extends TestCase
{
    public function testGetQrReturnsPayload(): void
    {
        $http = new FakeHttpClient([
            'Success'   => true,
            'PaymentId' => '777',
            'Data'      => 'https://qr.nspk.ru/example',
            'Status'    => 'NEW',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->getQr(
            new GetQrRequestDto(paymentId: '777'),
        );

        $this->assertSame('GetQr', $http->requests[0]['endpoint']);
        $this->assertSame('PAYLOAD', $http->requests[0]['payload']['DataType']);
        $this->assertTrue($response->hasQrData());
        $this->assertSame('https://qr.nspk.ru/example', $response->data);
    }

    public function testGetQrSupportsImageType(): void
    {
        $http = new FakeHttpClient([
            'Success'   => true,
            'PaymentId' => '777',
            'Data'      => '<svg>...</svg>',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->getQr(
            new GetQrRequestDto(
                paymentId: '777',
                dataType : QrDataTypeEnum::Image,
            ),
        );

        $this->assertSame('IMAGE', $http->requests[0]['payload']['DataType']);
        $this->assertStringStartsWith('<svg', $response->data ?? '');
    }

    public function testGetQrBankListSendsDeviceAndParsesBanks(): void
    {
        $http = new FakeHttpClient([
            'Success'  => true,
            'BankList' => [
                [
                    'BankId'     => 'bank-uuid-1',
                    'NspkBankId' => '100000000004',
                    'BankName'   => 'Т-Банк',
                    'BankLogo'   => 'https://qr.nspk.ru/logo.png',
                    'BankOrder'  => 1,
                ],
            ],
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->getQrBankList(
            new GetQrBankListRequestDto(
                device      : new DeviceDto(DeviceTypeEnum::Mobile, 'Android'),
                scenarioType: QrScenarioTypeEnum::Sub,
            ),
        );

        $this->assertSame('GetQrBankList', $http->requests[0]['endpoint']);
        $this->assertSame('sub', $http->requests[0]['payload']['ScenarioType']);
        $this->assertSame('mobile', $http->requests[0]['payload']['Device']['Type']);
        $this->assertSame('Android', $http->requests[0]['payload']['Device']['Os']);
        $this->assertCount(1, $response->bankList);
        $this->assertSame('100000000004', $response->bankList[0]->nspkBankId);
    }

    public function testQrMembersListParsesMembers(): void
    {
        $http = new FakeHttpClient([
            'Success' => true,
            'OrderId' => 'order-qr-1',
            'Members' => [
                [
                    'MemberId'   => '100000000111',
                    'MemberName' => 'Сбербанк',
                    'IsPayee'    => true,
                ],
            ],
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->qrMembersList('888');

        $this->assertSame('QrMembersList', $http->requests[0]['endpoint']);
        $this->assertSame('888', $http->requests[0]['payload']['PaymentId']);
        $this->assertSame('order-qr-1', $response->orderId);
        $this->assertTrue($response->members[0]->isPayee);
    }

    public function testChargeQrSendsAccountToken(): void
    {
        $http = new FakeHttpClient([
            'Success'   => true,
            'Status'    => 'CONFIRMED',
            'PaymentId' => '999',
            'OrderId'   => 'order-qr-2',
            'Amount'    => 50000,
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->chargeQr(
            new ChargeQrRequestDto(
                paymentId   : '999',
                accountToken: 'acc-token-1',
            ),
        );

        $this->assertSame('ChargeQr', $http->requests[0]['endpoint']);
        $this->assertSame('acc-token-1', $http->requests[0]['payload']['AccountToken']);
        $this->assertSame(PaymentStatusEnum::Confirmed, $response->status);
    }

    public function testAddAccountQrReturnsRequestKeyAndData(): void
    {
        $http = new FakeHttpClient([
            'Success'    => true,
            'RequestKey' => 'req-key-1',
            'Data'       => 'https://qr.nspk.ru/bind',
            'Status'     => 'NEW',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->addAccountQr(
            new AddAccountQrRequestDto(description: 'Привязка счёта'),
        );

        $this->assertSame('AddAccountQr', $http->requests[0]['endpoint']);
        $this->assertSame('PAYLOAD', $http->requests[0]['payload']['DataType']);
        $this->assertSame('req-key-1', $response->requestKey);
        $this->assertTrue($response->hasQrData());
    }

    public function testGetAddAccountQrStateParsesBindingStatus(): void
    {
        $http = new FakeHttpClient([
            'Success'        => true,
            'RequestKey'     => 'req-key-1',
            'Status'         => 'ACTIVE',
            'BankMemberId'   => '100000000004',
            'BankMemberName' => 'Т-Банк',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->sbp()->getAddAccountQrState('req-key-1');

        $this->assertSame('GetAddAccountQrState', $http->requests[0]['endpoint']);
        $this->assertSame('req-key-1', $http->requests[0]['payload']['RequestKey']);
        $this->assertSame(AccountQrStatusEnum::Active, $response->status);
        $this->assertTrue($response->status->isBound());
    }
}
