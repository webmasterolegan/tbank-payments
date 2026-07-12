<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Support\ApiValueParser;

final readonly class CardListResponseDto
{
    /** @param CardDto[] $cards */
    public function __construct(
        public bool $success,
        public array $cards = [],
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $cards = [];

        if (ApiValueParser::parseSuccess($data['Success'] ?? false)) {
            foreach ((array) ($data['Cards'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                /** @var array<string, mixed> $item */
                $cards[] = CardDto::fromArray($item);
            }
        }

        return new self(
            success   : ApiValueParser::parseSuccess($data['Success'] ?? false),
            cards     : $cards,
            errorCode : ApiValueParser::asNullableString($data['ErrorCode'] ?? null),
            message   : ApiValueParser::asNullableString($data['Message'] ?? null),
        );
    }
}
