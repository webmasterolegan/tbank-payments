<?php

declare(strict_types=1);

namespace TBank\Payments;

/**
 * Генерирует токен (подпись запроса) по алгоритму T-Bank.
 *
 * Алгоритм:
 *   1. Берём только скалярные (не вложенные) поля запроса + Password.
 *   2. Сортируем по ключу (ksort).
 *   3. Конкатенируем значения в одну строку.
 *   4. Хешируем SHA-256 (UTF-8).
 *
 * @see https://developer.tbank.ru/eacq/intro/developer/token
 */
final class TokenGenerator
{
    public function __construct(private readonly string $password) {}

    /**
     * @param array<string, mixed> $params Параметры запроса (до добавления Token).
     */
    public function generate(array $params): string
    {
        // Оставляем только скалярные значения корневого уровня
        $flat = array_filter($params, static fn(mixed $v): bool => is_scalar($v));

        // Добавляем пароль
        $flat['Password'] = $this->password;

        // Сортируем по ключу
        ksort($flat);

        // Конкатенируем значения (T-Bank ожидает строковое представление)
        $concatenated = implode('', array_map(self::normalizeScalar(...), array_values($flat)));

        return hash('sha256', $concatenated);
    }

    private static function normalizeScalar(mixed $value): string
    {
        return match (true) {
            is_bool($value)  => $value ? 'true' : 'false',
            is_string($value), is_int($value), is_float($value) => (string) $value,
            default          => '',
        };
    }

    /**
     * Возвращает массив params с добавленным Token.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function sign(array $params): array
    {
        $params['Token'] = $this->generate($params);

        return $params;
    }
}
