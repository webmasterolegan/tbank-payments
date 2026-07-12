<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/** Базовый URL API T-Bank (v2). */
enum EnvironmentEnum: string
{
    /** Боевая среда. */
    case Production = 'https://securepay.tinkoff.ru/v2';

    /** Тестовая среда (требуется whitelist IP у банка). */
    case Test = 'https://rest-api-test.tinkoff.ru/v2';

    public function baseUrl(): string
    {
        return $this->value;
    }
}
