<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

/** Участник СБП для возврата QR-платежа. */
final readonly class QrMemberDto
{
    public function __construct(
        public string $memberId,
        public string $memberName,
        public bool $isPayee,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            memberId  : ApiValueParser::asString($data['MemberId'] ?? null),
            memberName: ApiValueParser::asString($data['MemberName'] ?? null),
            isPayee   : ApiValueParser::parseSuccess($data['IsPayee'] ?? false),
        );
    }
}
