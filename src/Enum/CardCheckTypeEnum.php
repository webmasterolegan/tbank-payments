<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/**
 * Тип проверки карты при привязке (AddCard.CheckType).
 *
 * @see https://developer.tbank.ru/eacq/api/add-card
 */
enum CardCheckTypeEnum: string
{
    /** Сохранить карту без проверок; RebillId не возвращается. */
    case No = 'NO';

    /** Холд 0 ₽ с последующей отменой; RebillId для терминалов без 3DS. */
    case Hold = 'HOLD';

    /** Проверка 3DS и списание 0 ₽; RebillId только для карт с 3DS. */
    case ThreeDs = '3DS';

    /** Проверка поддержки 3DS; при отсутствии 3DS — списание 0 ₽. */
    case ThreeDsHold = '3DSHOLD';
}
