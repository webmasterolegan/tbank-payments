<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/** Статус привязки счёта СБП (GetAddAccountQrState). */
enum AccountQrStatusEnum: string
{
    case Active = 'ACTIVE';

    case Inactive = 'INACTIVE';

    case Unknown = 'UNKNOWN';

    public static function fromPayload(mixed $value): self
    {
        if (!is_string($value) || $value === '') {
            return self::Unknown;
        }

        $normalized = strtoupper($value);

        // API иногда возвращает опечатку INACITVE.
        if ($normalized === 'INACITVE') {
            $normalized = 'INACTIVE';
        }

        return self::tryFrom($normalized) ?? self::Unknown;
    }

    public function isBound(): bool
    {
        return $this === self::Active;
    }
}
