<?php

declare(strict_types=1);

namespace TBank\Payments;

use TBank\Payments\DTO\WebhookNotificationDto;
use TBank\Payments\Exceptions\InvalidWebhookSignatureException;
use TBank\Payments\Support\ApiValueParser;

/**
 * Обработчик входящих уведомлений (webhook) от T-Bank.
 *
 * T-Bank отправляет POST JSON/form на NotificationURL после каждого изменения статуса.
 * Нужно ответить строкой "OK" (без кавычек) с HTTP 200.
 *
 * @see https://developer.tbank.ru/eacq/intro/developer/notification
 */
final class WebhookHandler
{
    public function __construct(
        private readonly TokenGenerator $tokenGenerator,
        private readonly ?string $expectedTerminalKey = null,
    ) {}

    /**
     * Разбирает и проверяет подпись входящего уведомления.
     *
     * @param array<string, mixed>|string $payload JSON-строка или уже декодированный массив.
     *
     * @throws InvalidWebhookSignatureException Если подпись не совпадает.
     */
    #[\NoDiscard]
    public function handle(array|string $payload): WebhookNotificationDto
    {
        if (is_string($payload)) {
            /** @var array<string, mixed> $payload */
            $payload = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        }

        $receivedToken = ($payload['Token'] ?? null) |> ApiValueParser::asString(...);
        $dataWithoutToken = array_diff_key($payload, ['Token' => null]);

        $expectedToken = $this->tokenGenerator->generate($dataWithoutToken);

        if (!hash_equals($expectedToken, $receivedToken)) {
            throw new InvalidWebhookSignatureException('Webhook token validation failed: signature mismatch.');
        }

        if ($this->expectedTerminalKey !== null) {
            $terminalKey = ($payload['TerminalKey'] ?? null) |> ApiValueParser::asString(...);

            if (!hash_equals($this->expectedTerminalKey, $terminalKey)) {
                throw new InvalidWebhookSignatureException('Webhook token validation failed: unexpected TerminalKey.');
            }
        }

        return WebhookNotificationDto::fromArray($payload);
    }

    /** Возвращает стандартный ответ для T-Bank (подтверждение получения). */
    #[\NoDiscard]
    public function acknowledge(): string
    {
        return 'OK';
    }
}
