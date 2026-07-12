<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\AddCustomerRequestDto;
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class CustomerApiTest extends TestCase
{
    public function testAddCustomer(): void
    {
        $http = new FakeHttpClient([
            'Success'     => true,
            'CustomerKey' => 'user-42',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->customer()->add(
            new AddCustomerRequestDto(
                customerKey: 'user-42',
                email      : 'user@example.com',
                phone      : '+79001234567',
            ),
        );

        $this->assertSame('AddCustomer', $http->requests[0]['endpoint']);
        $this->assertSame('user@example.com', $http->requests[0]['payload']['Email']);
        $this->assertTrue($response->success);
        $this->assertSame('user-42', $response->customerKey);
    }

    public function testGetCustomer(): void
    {
        $http = new FakeHttpClient([
            'Success'     => true,
            'CustomerKey' => 'user-42',
            'Email'       => 'user@example.com',
            'Phone'       => '+79001234567',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->customer()->get('user-42');

        $this->assertSame('GetCustomer', $http->requests[0]['endpoint']);
        $this->assertSame('user-42', $http->requests[0]['payload']['CustomerKey']);
        $this->assertSame('user@example.com', $response->email);
        $this->assertSame('+79001234567', $response->phone);
    }

    public function testRemoveCustomer(): void
    {
        $http = new FakeHttpClient([
            'Success'     => true,
            'CustomerKey' => 'user-42',
        ]);

        $client = new TBankClient('TERM', 'secret', httpClient: $http);

        $response = $client->customer()->remove('user-42');

        $this->assertSame('RemoveCustomer', $http->requests[0]['endpoint']);
        $this->assertTrue($response->success);
        $this->assertSame('user-42', $response->customerKey);
    }
}
