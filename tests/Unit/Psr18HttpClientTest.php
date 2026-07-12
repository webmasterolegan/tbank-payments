<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TBank\Payments\Enum\PaymentStatusEnum;
use TBank\Payments\Exceptions\ApiException;
use TBank\Payments\Http\Psr18HttpClient;
use TBank\Payments\TBankClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

final class Psr18HttpClientTest extends TestCase
{
    public function testPsr18ClientParsesSuccessfulResponse(): void
    {
        $psr17 = new Psr17Factory();

        $httpClient = new Psr18HttpClient(
            client        : new class(new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'Success'   => true,
                    'Status'    => 'NEW',
                    'PaymentId' => '123',
                    'OrderId'   => 'order-1',
                    'Amount'    => 5000,
                ], JSON_THROW_ON_ERROR),
            )) implements ClientInterface {
                public function __construct(private readonly ResponseInterface $response) {}

                public RequestInterface $lastRequest;

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    $this->lastRequest = $request;

                    return $this->response;
                }
            },
            requestFactory: $psr17,
            streamFactory : $psr17,
            baseUrl       : 'https://securepay.tinkoff.ru/v2',
        );

        $client = new TBankClient('TERM', 'secret', httpClient: $httpClient);

        $response = $client->status()->getState('123');

        $this->assertSame(PaymentStatusEnum::New, $response->status);
        $this->assertSame('123', $response->paymentId);
    }

    public function testPsr18ClientThrowsApiExceptionOnBusinessError(): void
    {
        $psr17 = new Psr17Factory();

        $httpClient = new Psr18HttpClient(
            client        : new class(new Response(
                400,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'Success'   => false,
                    'Message'   => 'Declined',
                    'ErrorCode' => '101',
                ], JSON_THROW_ON_ERROR),
            )) implements ClientInterface {
                public function __construct(private readonly ResponseInterface $response) {}

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    return $this->response;
                }
            },
            requestFactory: $psr17,
            streamFactory : $psr17,
            baseUrl       : 'https://securepay.tinkoff.ru/v2',
        );

        $client = new TBankClient('TERM', 'secret', httpClient: $httpClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Declined');

        (void) $client->status()->getState('123');
    }
}
