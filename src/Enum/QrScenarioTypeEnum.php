<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/** Тип сценария для GetQrBankList. */
enum QrScenarioTypeEnum: string
{
    /** Оплата. */
    case Qr = 'qr';

    /** Привязка счёта. */
    case Sub = 'sub';
}
