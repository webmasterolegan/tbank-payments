<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\AccountQrStatusEnum;
use TBank\Payments\Support\ApiValueParser;

/** Ответ метода GetAddAccountQrState. */
final readonly class GetAddAccountQrStateResponseDto
{
    public function __construct(
        public bool $success,
        public string $requestKey,
        public AccountQrStatusEnum $status,
        public ?string $bankMemberId = null,
        public ?string $bankMemberName = null,
        public ?string $terminalKey = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            success       : ApiValueParser::parseSuccess($data['Success'] ?? false),
            requestKey    : ApiValueParser::asString($data['RequestKey'] ?? null),
            status        : AccountQrStatusEnum::fromPayload($data['Status'] ?? null),
            bankMemberId  : ApiValueParser::asNullableString($data['BankMemberId'] ?? null),
            bankMemberName: ApiValueParser::asNullableString($data['BankMemberName'] ?? null),
            terminalKey   : ApiValueParser::asNullableString($data['TerminalKey'] ?? null),
            errorCode     : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message       : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
