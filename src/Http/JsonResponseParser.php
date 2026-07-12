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
    public static function parse(string $raw, int $httpCode, string $url = ''): array
    {
        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new NetworkException(
                sprintf('Invalid JSON response (HTTP %d): %s%s', $httpCode, $e->getMessage(), self::urlSuffix($url)),
                previous: $e,
            );
        }

        if ($httpCode >= 500) {
            $reason = 'Server error';

            if (isset($data['Message'])) {
                $reason = ApiValueParser::asString($data['Message'], $reason);
            }

            throw self::httpError($httpCode, $reason, $url);
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
            sprintf('HTTP error [%d]: %s%s', $httpCode, $reason, self::urlSuffix($url)),
        );
    }

    private static function urlSuffix(string $url): string
    {
        return $url !== '' ? sprintf(' (URL: %s)', $url) : '';
    }
}
