<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/**
 * Базовый класс для всех request-DTO.
 *
 * Предоставляет вспомогательный метод filterNulls(), который убирает null-значения
 * из массива параметров перед отправкой в API. Это позволяет записывать toArray()
 * декларативно — одним выражением array — не перегружая метод цепочками if.
 */
abstract readonly class BaseRequestDto
{
    /**
     * Убирает null-значения из массива параметров запроса.
     *
     * Используется в toArray() наследников для формирования тела запроса:
     * поля, которые не были переданы (null), не включаются в итоговый массив.
     *
     * @param array<string, mixed> $map
     * @return array<string, mixed>
     */
    protected function filterNulls(array $map): array
    {
        return array_filter($map, static fn(mixed $v) => $v !== null);
    }
}
