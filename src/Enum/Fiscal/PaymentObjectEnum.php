<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Fiscal;

/**
 * Признак предмета расчёта (Receipt.Items.PaymentObject, тег ФФД 1212).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum PaymentObjectEnum: string
{
    /** Товар (значение по умолчанию в API). */
    case Commodity = 'commodity';

    /** Подакцизный товар. */
    case Excise = 'excise';

    /** Работа. */
    case Job = 'job';

    /** Услуга. */
    case Service = 'service';

    /** Ставка азартной игры. */
    case GamblingBet = 'gambling_bet';

    /** Выигрыш азартной игры. */
    case GamblingPrize = 'gambling_prize';

    /** Лотерейный билет. */
    case Lottery = 'lottery';

    /** Выигрыш лотереи. */
    case LotteryPrize = 'lottery_prize';

    /** Результаты интеллектуальной деятельности. */
    case IntellectualActivity = 'intellectual_activity';

    /** Платёж. */
    case Payment = 'payment';

    /** Агентское вознаграждение. */
    case AgentCommission = 'agent_commission';

    /** Составной предмет расчёта. */
    case Composite = 'composite';

    /** Иной предмет расчёта. */
    case Another = 'another';

    /** Взнос. */
    case Contribution = 'contribution';

    /** Имущественное право. */
    case PropertyRights = 'property_rights';

    /** Внереализационный доход. */
    case Unrealization = 'unrealization';

    /** Иные платежи и взносы, уменьшающие налог. */
    case TaxReduction = 'tax_reduction';

    /** Торговый сбор. */
    case TradeFee = 'trade_fee';

    /** Курортный сбор. */
    case ResortTax = 'resort_tax';

    /** Залог. */
    case Pledge = 'pledge';

    /** Расходы уменьшающие доход. */
    case IncomeDecrease = 'income_decrease';

    /** Взносы на ОПС ИП (без выплат физлицам). */
    case IePensionInsuranceWithoutPayments = 'ie_pension_insurance_without_payments';

    /** Взносы на ОПС ИП (с выплатами физлицам). */
    case IePensionInsuranceWithPayments = 'ie_pension_insurance_with_payments';

    /** Взносы на ОМС ИП (без выплат физлицам). */
    case IeMedicalInsuranceWithoutPayments = 'ie_medical_insurance_without_payments';

    /** Взносы на ОМС ИП (с выплатами физлицам). */
    case IeMedicalInsuranceWithPayments = 'ie_medical_insurance_with_payments';

    /** Взносы на обязательное социальное страхование. */
    case SocialInsurance = 'social_insurance';

    /** Платежи казино (приобретение фишек). */
    case CasinoChips = 'casino_chips';

    /** Выдача денежных средств платёжным агентом. */
    case AgentPayment = 'agent_payment';

    /** Подакцизный товар без кода маркировки. */
    case ExcisableGoodsWithoutMarkingCode = 'excisable_goods_without_marking_code';

    /** Подакцизный товар с кодом маркировки. */
    case ExcisableGoodsWithMarkingCode = 'excisable_goods_with_marking_code';

    /** Товар без кода маркировки. */
    case GoodsWithoutMarkingCode = 'goods_without_marking_code';

    /** Товар с кодом маркировки. */
    case GoodsWithMarkingCode = 'goods_with_marking_code';
}
