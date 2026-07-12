<?php

declare(strict_types=1);

namespace TBank\Payments\Http;

use TBank\Payments\Exceptions\NetworkException;

/**
 * Декоратор HTTP-клиента с повторными попытками при сетевых ошибках.
 *
 * Повторяет только {@see NetworkException}; ошибки API (success=false) не ретраятся.
 */
final class RetryingHttpClient implements HttpClientContract
{
    public function __construct(
        private readonly HttpClientContract $inner,
        private readonly int $maxAttempts = 3,
        private readonly int $delayMs = 200,
    ) {
        if ($maxAttempts < 1) {
            throw new \InvalidArgumentException('maxAttempts must be at least 1');
        }
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $payload): array
    {
        $attempt = 0;

        while (true) {
            try {
                return $this->inner->post($endpoint, $payload);
            } catch (NetworkException $e) {
                $attempt++;

                if ($attempt >= $this->maxAttempts) {
                    throw $e;
                }

                usleep($this->delayMs * 1000 * $attempt);
            }
        }
    }
}
