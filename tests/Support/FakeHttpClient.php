<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Support;

use TBank\Payments\Http\HttpClientContract;

/** HTTP-клиент-заглушка для unit-тестов API. */
final class FakeHttpClient implements HttpClientContract
{
    /** @var list<array{endpoint: string, payload: array<string, mixed>}> */
    public array $requests = [];

    /**
     * @param array<string, mixed>|callable(string, array<string, mixed>): array<string, mixed> $response
     */
    public function __construct(
        private mixed $response = ['Success' => true],
    ) {}

    public function post(string $endpoint, array $payload): array
    {
        $this->requests[] = ['endpoint' => $endpoint, 'payload' => $payload];

        if (is_callable($this->response)) {
            return ($this->response)($endpoint, $payload);
        }

        return $this->response;
    }
}
