<?php

declare(strict_types=1);

namespace TBank\Payments\Support;

use TBank\Payments\Enum\PaymentStatusEnum;

/** Разбор скалярных значений из ответов и webhook T-Bank. */
final class ApiValueParser
{
    /** T-Bank передаёт Success как bool или строку "true"/"false". */
    public static function parseSuccess(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return strtolower($value) === 'true' || $value === '1';
        }

        if (is_int($value) || is_float($value)) {
            return $value !== 0;
        }

        return false;
    }

    public static function asString(mixed $value, string $default = ''): string
    {
        if ($value === null) {
            return $default;
        }

        if (is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return $default;
    }

    public static function asInt(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    public static function asNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return null;
    }

    public static function asPaymentStatus(mixed $value): PaymentStatusEnum
    {
        return PaymentStatusEnum::tryFrom(self::asString($value)) ?? PaymentStatusEnum::Unknown;
    }
}
