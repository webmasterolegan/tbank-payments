<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

/** Ответ метода QrMembersList — банки для возврата QR-платежа. */
final readonly class QrMembersListResponseDto
{
    /** @param QrMemberDto[] $members */
    public function __construct(
        public bool $success,
        public array $members = [],
        public ?string $orderId = null,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $members = [];

        if (ApiValueParser::parseSuccess($data['Success'] ?? false)) {
            foreach ((array) ($data['Members'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                /** @var array<string, mixed> $item */
                $members[] = QrMemberDto::fromArray($item);
            }
        }

        return new self(
            success  : ApiValueParser::parseSuccess($data['Success'] ?? false),
            members  : $members,
            orderId  : ApiValueParser::asNullableString($data['OrderId'] ?? null),
            errorCode: ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message  : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
