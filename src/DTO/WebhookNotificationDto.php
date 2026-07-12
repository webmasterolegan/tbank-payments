<?php

declare(strict_types=1);

namespace TBank\Payments\DTO;

use TBank\Payments\Enum\{NotificationTypeEnum, PaymentStatusEnum};
use TBank\Payments\Support\ApiValueParser;

/** Валидированное уведомление от T-Bank (webhook). */
final readonly class WebhookNotificationDto
{
    /** @var list<string> */
    private const SENSITIVE_RAW_KEYS = ['Pan', 'ExpDate', 'CardId', 'AccountToken'];

    /**
     * @param array<string, mixed> $raw Полный payload после проверки подписи.
     *
     * @warning Поле $raw может содержать чувствительные данные: Pan, ExpDate, CardId,
     *          AccountToken. Не передавайте его в логи напрямую — используйте
     *          {@see rawRedacted()} или {@see withoutSensitiveData()} перед логированием.
     */
    public function __construct(
        public string $terminalKey,
        public string $orderId,
        public string $paymentId,
        public PaymentStatusEnum $status,
        public int $amount,
        public bool $success,
        public NotificationTypeEnum $notificationType = NotificationTypeEnum::Payment,
        public ?string $rebillId = null,
        public ?string $cardId = null,
        public ?string $pan = null,
        public ?string $expDate = null,
        public ?string $accountToken = null,
        public ?string $requestKey = null,
        public ?string $errorCode = null,
        public ?string $message = null,
        /** @see rawRedacted() для безопасного логирования без чувствительных данных */
        public array $raw = [],
    ) {}

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            terminalKey      : ($payload['TerminalKey'] ?? null) |> ApiValueParser::asString(...),
            orderId          : ($payload['OrderId'] ?? null) |> ApiValueParser::asString(...),
            paymentId        : ($payload['PaymentId'] ?? null) |> ApiValueParser::asString(...),
            status           : ($payload['Status'] ?? null) |> ApiValueParser::asPaymentStatus(...),
            amount           : ($payload['Amount'] ?? null) |> ApiValueParser::asInt(...),
            success          : ApiValueParser::parseSuccess($payload['Success'] ?? false),
            notificationType : NotificationTypeEnum::fromPayload($payload['NotificationType'] ?? null),
            rebillId         : ($payload['RebillId'] ?? null) |> ApiValueParser::asNullableString(...),
            cardId           : ($payload['CardId'] ?? null) |> ApiValueParser::asNullableString(...),
            pan              : ($payload['Pan'] ?? null) |> ApiValueParser::asNullableString(...),
            expDate          : ($payload['ExpDate'] ?? null) |> ApiValueParser::asNullableString(...),
            accountToken     : ($payload['AccountToken'] ?? null) |> ApiValueParser::asNullableString(...),
            requestKey       : ($payload['RequestKey'] ?? null) |> ApiValueParser::asNullableString(...),
            errorCode        : ($payload['ErrorCode'] ?? null) |> ApiValueParser::asNullableString(...),
            message          : ($payload['Message'] ?? null) |> ApiValueParser::asNullableString(...),
            raw              : $payload,
        );
    }

    /** Копия без чувствительных полей (clone with, PHP 8.5). */
    public function withoutSensitiveData(): self
    {
        return clone($this, [
            'pan'          => null,
            'expDate'      => null,
            'cardId'       => null,
            'accountToken' => null,
            'raw'          => self::redactRawPayload($this->raw),
        ]);
    }

    /**
     * Payload без чувствительных данных (для логов).
     *
     * @return array<string, mixed>
     */
    public function rawRedacted(): array
    {
        return $this->withoutSensitiveData()->raw;
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private static function redactRawPayload(array $raw): array
    {
        $redacted = $raw;

        foreach (self::SENSITIVE_RAW_KEYS as $key) {
            unset($redacted[$key]);
        }

        return $redacted;
    }
}
