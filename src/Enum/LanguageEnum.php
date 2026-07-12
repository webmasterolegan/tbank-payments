<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/**
 * Язык платёжной формы (Init.Language).
 *
 * @see https://developer.tbank.ru/eacq/api/init
 */
enum LanguageEnum: string
{
    /** Русский (значение по умолчанию в API). */
    case Ru = 'ru';

    /** Английский. */
    case En = 'en';
}
