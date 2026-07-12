<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

/** Ответ метода GetQrBankList. */
final readonly class GetQrBankListResponseDto
{
    /** @param QrBankDto[] $bankList */
    public function __construct(
        public bool $success,
        public array $bankList = [],
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $bankList = [];

        if (ApiValueParser::parseSuccess($data['Success'] ?? false)) {
            foreach ((array) ($data['BankList'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                /** @var array<string, mixed> $item */
                $bankList[] = QrBankDto::fromArray($item);
            }
        }

        return new self(
            success  : ApiValueParser::parseSuccess($data['Success'] ?? false),
            bankList : $bankList,
            errorCode: ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message  : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
