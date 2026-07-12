<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Exceptions\NetworkException;
use TBank\Payments\Http\{HttpClientContract, RetryingHttpClient};
use TBank\Payments\TBankClient;
use TBank\Payments\Tests\Support\FakeHttpClient;

final class RetryingHttpClientTest extends TestCase
{
    public function testRetriesOnNetworkException(): void
    {
        $attempts = 0;
        $inner = new class($attempts) implements HttpClientContract {
            public function __construct(private int &$attempts) {}

            public function post(string $endpoint, array $payload): array
            {
                $this->attempts++;

                if ($this->attempts < 3) {
                    throw new NetworkException('temporary failure');
                }

                return ['Success' => true, 'PaymentId' => '1'];
            }
        };

        $client = new TBankClient(
            terminalKey   : 'TERM',
            password      : 'secret',
            httpClient    : new RetryingHttpClient($inner, maxAttempts: 3, delayMs: 0),
        );

        $response = $client->status()->getState('1');

        $this->assertSame(3, $attempts);
        $this->assertSame('1', $response->paymentId);
    }

    public function testDoesNotRetryApiErrors(): void
    {
        $http = new FakeHttpClient(function (): never {
            throw new \TBank\Payments\Exceptions\ApiException('declined', '101');
        });

        $client = new TBankClient(
            terminalKey: 'TERM',
            password   : 'secret',
            httpClient : new RetryingHttpClient($http, maxAttempts: 3, delayMs: 0),
        );

        $this->expectException(\TBank\Payments\Exceptions\ApiException::class);

        (void) $client->status()->getState('1');

        $this->assertCount(1, $http->requests);
    }

    public function testBackoffDelayGrowsExponentially(): void
    {
        $delays = [];

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $delays[] = (int) (200 * 1000 * (2 ** ($attempt - 1)));
        }

        $this->assertSame([200_000, 400_000, 800_000], $delays);
    }

    public function testClientWrapsHttpClientWhenRetryAttemptsSet(): void
    {
        $attempts = 0;
        $inner = new class($attempts) implements HttpClientContract {
            public function __construct(private int &$attempts) {}

            public function post(string $endpoint, array $payload): array
            {
                $this->attempts++;

                if ($this->attempts < 2) {
                    throw new NetworkException('temporary failure');
                }

                return ['Success' => true, 'PaymentId' => '42'];
            }
        };

        $client = new TBankClient(
            terminalKey   : 'TERM',
            password      : 'secret',
            httpClient    : $inner,
            retryAttempts : 2,
            retryDelayMs  : 0,
        );

        $response = $client->status()->getState('42');

        $this->assertSame(2, $attempts);
        $this->assertSame('42', $response->paymentId);
    }
}
