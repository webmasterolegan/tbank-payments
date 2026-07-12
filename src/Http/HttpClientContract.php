<?php

declare(strict_types=1);

namespace TBank\Payments\Http;

interface HttpClientContract
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $payload): array;
}
