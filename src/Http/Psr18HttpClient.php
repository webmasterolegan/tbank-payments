<?php

declare(strict_types=1);

namespace TBank\Payments\Http;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TBank\Payments\Exceptions\NetworkException;
use TBank\Payments\Support\ApiUrlBuilder;

/**
 * HTTP-клиент на PSR-18 для взаимодействия с T-Bank API.
 *
 * Требует PSR-18-совместимую реализацию (Guzzle, Symfony HttpClient и т.д.).
 */
final class Psr18HttpClient implements HttpClientContract
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly string $baseUrl,
    ) {}

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $payload): array
    {
        $url  = ApiUrlBuilder::join($this->baseUrl, $endpoint);
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $request = $this->requestFactory
            ->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json')
            ->withBody($this->streamFactory->createStream($body));

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new NetworkException(
                sprintf('PSR-18 client error: %s (URL: %s)', $e->getMessage(), $url),
                previous: $e,
            );
        }

        return JsonResponseParser::parse(
            (string) $response->getBody(),
            $response->getStatusCode(),
            $url,
        );
    }
}
