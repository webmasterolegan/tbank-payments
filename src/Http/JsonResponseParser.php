<?php

declare(strict_types=1);

namespace TBank\Payments\Http;

use TBank\Payments\Exceptions\{ApiException, NetworkException};
use TBank\Payments\Support\ApiValueParser;

/** Разбор JSON-ответов T-Bank API. */
final class JsonResponseParser
{
    /**
     * @return array<string, mixed>
     *
     * @throws NetworkException Если HTTP 5xx или невалидный JSON.
     * @throws ApiException     Если API вернул success=false.
     */
    public static function parse(string $raw, int $httpCode): array
    {
        if ($httpCode >= 500) {
            throw self::httpError($httpCode, 'Server error', '');
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new NetworkException(
                sprintf('Invalid JSON response (HTTP %d): %s', $httpCode, $e->getMessage()),
                previous: $e,
            );
        }

        if (array_key_exists('Success', $data) && !ApiValueParser::parseSuccess($data['Success'])) {
            throw new ApiException(
                message  : ApiValueParser::asString($data['Message'] ?? null, 'Unknown API error'),
                errorCode: ApiValueParser::asString($data['ErrorCode'] ?? null, '0'),
                details  : $data['Details'] ?? null,
                httpCode : $httpCode,
            );
        }

        return $data;
    }

    public static function networkError(int $errno, string $error, string $url): NetworkException
    {
        return new NetworkException(
            sprintf('cURL error [%d]: %s (URL: %s)', $errno, $error, $url),
        );
    }

    public static function httpError(int $httpCode, string $reason, string $url): NetworkException
    {
        return new NetworkException(
            sprintf('HTTP error [%d]: %s (URL: %s)', $httpCode, $reason, $url),
        );
    }
}
