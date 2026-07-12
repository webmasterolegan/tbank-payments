<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

/** Банк-участник СБП из ответа GetQrBankList. */
final readonly class QrBankDto
{
    public function __construct(
        public string $bankId,
        public string $nspkBankId,
        public string $bankName,
        public int $bankOrder,
        public ?string $bankLogo = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            bankId    : ApiValueParser::asString($data['BankId'] ?? null),
            nspkBankId: ApiValueParser::asString($data['NspkBankId'] ?? null),
            bankName  : ApiValueParser::asString($data['BankName'] ?? null),
            bankOrder : ApiValueParser::asInt($data['BankOrder'] ?? null),
            bankLogo  : ApiValueParser::asNullableString($data['BankLogo'] ?? null),
        );
    }
}
