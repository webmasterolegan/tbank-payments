<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Fiscal;

/**
 * Система налогообложения (Receipt.Taxation, тег ФФД 1055).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum TaxationEnum: string
{
    /** Общая система налогообложения. */
    case Osn = 'osn';

    /** УСН «доходы». */
    case UsnIncome = 'usn_income';

    /** УСН «доходы минус расходы». */
    case UsnIncomeOutcome = 'usn_income_outcome';

    /** Единый сельскохозяйственный налог. */
    case Esn = 'esn';

    /** Патентная система налогообложения. */
    case Patent = 'patent';
}
