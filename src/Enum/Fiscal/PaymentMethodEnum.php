<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Fiscal;

/**
 * Признак способа расчёта (Receipt.Items.PaymentMethod, тег ФФД 1214).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum PaymentMethodEnum: string
{
    /** Предоплата 100%. */
    case FullPrepayment = 'full_prepayment';

    /** Предоплата. */
    case Prepayment = 'prepayment';

    /** Аванс. */
    case Advance = 'advance';

    /** Полный расчёт (значение по умолчанию в API). */
    case FullPayment = 'full_payment';

    /** Частичный расчёт и кредит. */
    case PartialPayment = 'partial_payment';

    /** Передача в кредит. */
    case Credit = 'credit';

    /** Оплата кредита. */
    case CreditPayment = 'credit_payment';
}
