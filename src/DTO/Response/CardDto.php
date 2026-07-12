<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Response;

use TBank\Payments\Enum\Card\{CardStatusEnum, CardTypeEnum};
use TBank\Payments\Support\ApiValueParser;

final readonly class CardDto
{
    public function __construct(
        public string $cardId,
        public string $pan,
        public string $expDate,
        public ?CardTypeEnum $cardType,
        public ?CardStatusEnum $status,
        public ?string $rebillId = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $cardTypeRaw = $data['CardType'] ?? null;

        return new self(
            cardId  : ApiValueParser::asString($data['CardId'] ?? null),
            pan     : ApiValueParser::asString($data['Pan'] ?? null),
            expDate : ApiValueParser::asString($data['ExpDate'] ?? null),
            cardType: is_numeric($cardTypeRaw)
                ? CardTypeEnum::tryFrom((int) $cardTypeRaw)
                : null,
            status  : isset($data['Status'])
                ? CardStatusEnum::tryFrom(ApiValueParser::asString($data['Status']))
                : null,
            rebillId: ApiValueParser::asNullableString($data['RebillId'] ?? null),
        );
    }
}
