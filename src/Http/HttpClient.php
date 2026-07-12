<?php

declare(strict_types=1);

namespace TBank\Payments\Http;

use CurlSharePersistentHandle;
use TBank\Payments\Exceptions\{ApiException, NetworkException};
use TBank\Payments\Support\ApiUrlBuilder;

/**
 * HTTP-клиент на cURL для взаимодействия с T-Bank API.
 * Поддерживает TLS 1.2+, отправляет JSON, возвращает декодированный массив.
 */
final class HttpClient implements HttpClientContract
{
    private static ?CurlSharePersistentHandle $persistentShare = null;

    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
        private readonly bool $reuseConnection = false,
    ) {}

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     *
     * @throws NetworkException Если cURL не смог выполнить запрос.
     * @throws ApiException     Если API вернул success=false.
     */
    public function post(string $endpoint, array $payload): array
    {
        $url  = ApiUrlBuilder::join($this->baseUrl, $endpoint);
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $ch = curl_init();
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2 | CURL_SSLVERSION_MAX_DEFAULT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ];

        if ($this->reuseConnection) {
            $options[CURLOPT_SHARE] = $this->persistentShare();
        }

        curl_setopt_array($ch, $options);

        $raw      = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno || $raw === false) {
            throw JsonResponseParser::networkError($errno, $error, $url);
        }

        return JsonResponseParser::parse((string) $raw, $httpCode);
    }

    private function persistentShare(): CurlSharePersistentHandle
    {
        return self::$persistentShare ??= curl_share_init_persistent([
            CURL_LOCK_DATA_DNS,
            CURL_LOCK_DATA_CONNECT,
        ]);
    }
}
