<?php

declare(strict_types=1);

namespace TBank\Payments\Support;

use Uri\Rfc3986\Uri;

/** Сборка URL для запросов T-Bank API (RFC 3986). */
final class ApiUrlBuilder
{
    /**
     * @return non-empty-string
     */
    public static function join(string $baseUrl, string $endpoint): string
    {
        $normalizedBase = rtrim($baseUrl, '/') . '/';
        $base = Uri::parse($normalizedBase);

        if ($base === null) {
            throw new \InvalidArgumentException(sprintf('Invalid base URL: %s', $baseUrl));
        }

        $relative = ltrim($endpoint, '/');
        $resolved = Uri::parse($relative, $base);

        if ($resolved === null) {
            throw new \InvalidArgumentException(sprintf('Invalid endpoint: %s', $endpoint));
        }

        $url = $resolved->toString();

        if ($url === '') {
            throw new \InvalidArgumentException(sprintf('Resolved URL is empty for endpoint: %s', $endpoint));
        }

        return $url;
    }
}
