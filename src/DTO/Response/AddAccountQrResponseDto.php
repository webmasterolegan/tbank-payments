<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\{PaymentStatusEnum, QrDataTypeEnum};
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода AddAccountQr. */
final readonly class AddAccountQrResponseDto
{
    public function __construct(
        public bool $success,
        public string $requestKey,
        /** Payload СБП или SVG-изображение QR. */
        public ?string $data = null,
        public PaymentStatusEnum $status = PaymentStatusEnum::Unknown,
        public QrDataTypeEnum $dataType = QrDataTypeEnum::Payload,
        public ?string $terminalKey = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $dataTypeRaw = $data['DataType'] ?? null;

        return new self(
            success    : ApiValueParser::parseSuccess($data['Success'] ?? false),
            requestKey : ApiValueParser::asString($data['RequestKey'] ?? null),
            data       : ApiValueParser::asNullableString($data['Data'] ?? null),
            status     : ApiValueParser::asPaymentStatus($data['Status'] ?? null),
            dataType   : is_string($dataTypeRaw)
                ? QrDataTypeEnum::tryFrom(strtoupper($dataTypeRaw)) ?? QrDataTypeEnum::Payload
                : QrDataTypeEnum::Payload,
            terminalKey: ApiValueParser::asNullableString($data['TerminalKey'] ?? null),
            errorCode  : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message    : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }

    public function hasQrData(): bool
    {
        return $this->data !== null && $this->data !== '';
    }
}
