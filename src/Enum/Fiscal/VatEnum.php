<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Fiscal;

/**
 * Ставка НДС (Receipt.Items.Tax).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum VatEnum: string
{
    /** Без НДС. */
    case None = 'none';

    /** НДС 0%. */
    case Vat0 = 'vat0';

    /** НДС 5%. */
    case Vat5 = 'vat5';

    /** НДС 7%. */
    case Vat7 = 'vat7';

    /** НДС 10%. */
    case Vat10 = 'vat10';

    /** НДС 22%. */
    case Vat22 = 'vat22';

    /** НДС по расчётной ставке 5/105. */
    case Vat105 = 'vat105';

    /** НДС по расчётной ставке 7/107. */
    case Vat107 = 'vat107';

    /** НДС по расчётной ставке 10/110. */
    case Vat110 = 'vat110';

    /** НДС по расчётной ставке 22/122. */
    case Vat122 = 'vat122';
}
