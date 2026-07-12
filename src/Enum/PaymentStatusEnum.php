<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/**
 * Статусы платежа T-Bank.
 *
 * @see https://developer.tbank.ru/eacq/intro/developer/glossary
 */
enum PaymentStatusEnum: string
{
    /** Платёж создан, ожидает ввода данных карты. */
    case New = 'NEW';

    /** Платёжная форма открыта покупателем. */
    case FormShowed = 'FORM_SHOWED';

    /** Выполняется 3DS-верификация. */
    case ThreeDsChecking = '3DS_CHECKING';

    /** 3DS успешно пройдена. */
    case ThreeDsChecked = '3DS_CHECKED';

    /** Авторизация выполнена, ожидает подтверждения (двухстадийный платёж). */
    case Authorized = 'AUTHORIZED';

    /** Средства успешно списаны. */
    case Confirmed = 'CONFIRMED';

    /** Выполнен полный возврат. */
    case Refunded = 'REFUNDED';

    /** Выполнен частичный возврат. */
    case PartialRefunded = 'PARTIAL_REFUNDED';

    /** Платёж отклонён. */
    case Rejected = 'REJECTED';

    /** Авторизация отменена (двухстадийный платёж). */
    case Reversed = 'REVERSED';

    /** Платёж ожидает действий покупателя (например, оплаты через СБП). */
    case Waiting = 'WAITING';

    /** Неизвестный или новый статус, ещё не добавленный в SDK. */
    case Unknown = 'UNKNOWN';

    public function isSuccessful(): bool
    {
        return $this === self::Confirmed;
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::Confirmed,
            self::Refunded,
            self::PartialRefunded,
            self::Rejected,
            self::Reversed => true,
            default        => false,
        };
    }
}
