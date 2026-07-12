<?php

declare(strict_types=1);

/**
 * Общая инициализация для примеров.
 *
 * Переменные окружения:
 *   TBANK_TERMINAL_KEY — ключ терминала
 *   TBANK_PASSWORD     — пароль терминала
 *   TBANK_ENV          — production (по умолчанию) или test
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use TBank\Payments\Enum\EnvironmentEnum;
use TBank\Payments\TBankClient;

function createClient(): TBankClient
{
    $terminalKey = getenv('TBANK_TERMINAL_KEY') ?: 'YOUR_TERMINAL_KEY';
    $password    = getenv('TBANK_PASSWORD') ?: 'YOUR_PASSWORD';
    $environment = getenv('TBANK_ENV') === 'test'
        ? EnvironmentEnum::Test
        : EnvironmentEnum::Production;

    return new TBankClient(
        terminalKey: $terminalKey,
        password   : $password,
        environment: $environment,
    );
}

function uniqueOrderId(string $prefix = 'order'): string
{
    return $prefix . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
}
