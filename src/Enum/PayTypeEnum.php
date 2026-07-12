<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/**
 * Тип проведения платежа (Init.PayType).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum PayTypeEnum: string
{
    /** Одностадийная оплата (списание сразу). */
    case OneStep = 'O';

    /** Двухстадийная оплата (холд + Confirm). */
    case TwoStep = 'T';
}
