<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/**
 * Тип webhook-уведомления T-Bank.
 *
 * @see https://developer.tbank.ru/eacq/intro/developer/notification
 */
enum NotificationTypeEnum: string
{
    /** Платёж (по умолчанию, если поле отсутствует). */
    case Payment = 'PAYMENT';

    /** Привязка карты. */
    case LinkCard = 'LINKCARD';

    /** Фискализация. */
    case Fiscalization = 'FISCALIZATION';

    /** Статус привязки счёта по QR. */
    case Qr = 'QR';

    /** Неизвестный тип. */
    case Unknown = 'UNKNOWN';

    public static function fromPayload(mixed $value): self
    {
        if (!is_string($value) || $value === '') {
            return self::Payment;
        }

        $normalized = strtoupper($value);

        return self::tryFrom($normalized) ?? self::Unknown;
    }
}
