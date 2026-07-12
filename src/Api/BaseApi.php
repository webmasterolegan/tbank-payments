<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\Http\HttpClientContract;
use TBank\Payments\TokenGenerator;

/**
 * Базовый класс для всех API эквайринга T-Bank.
 */
abstract class BaseApi
{
    /**
     * @param HttpClientContract $http
     * @param TokenGenerator $token
     * @param string $terminalKey
     */
    public function __construct(
        protected readonly HttpClientContract $http,
        protected readonly TokenGenerator $token,
        protected readonly string $terminalKey,
    ) {}

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    protected function request(string $endpoint, array $params): array
    {
        $params['TerminalKey'] = $this->terminalKey;

        return $this->http->post($endpoint, $this->token->sign($params));
    }
}
