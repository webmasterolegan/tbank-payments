<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Card;

/**
 * Статус привязанной карты (GetCardList.Status).
 *
 * @see https://developer.tbank.ru/eacq/api/get-card-list
 */
enum CardStatusEnum: string
{
    /** Карта активна. */
    case Active = 'A';

    /** Карта удалена / неактивна. */
    case Deleted = 'D';

    /** Срок действия карты истёк. */
    case Expired = 'E';
}
