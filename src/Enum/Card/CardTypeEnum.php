<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Card;

/**
 * Тип платёжной системы (GetCardList.CardType).
 *
 * API передаёт числовой идентификатор.
 *
 * @see https://developer.tbank.ru/eacq/api/get-card-list
 */
enum CardTypeEnum: int
{
    /** Visa. */
    case Visa = 0;

    /** Mastercard. */
    case Mastercard = 1;

    /** МИР. */
    case Mir = 2;
}
