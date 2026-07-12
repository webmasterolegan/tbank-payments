<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/** Тип устройства для GetQrBankList. */
enum DeviceTypeEnum: string
{
    case Desktop = 'desktop';

    case Mobile = 'mobile';
}
