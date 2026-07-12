<?php

declare(strict_types=1);

namespace TBank\Payments\Enum\Fiscal;

/** Версия формата фискальных данных (Receipt.FfdVersion). */
enum FfdVersionEnum: string
{
    /** ФФД 1.05. */
    case Version105 = '1.05';

    /** ФФД 1.2. */
    case Version12 = '1.2';
}
