<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Shared;

use TBank\Payments\Enum\Fiscal\{FfdVersionEnum, TaxationEnum};

/**
 * Объект чека (ReceiptDto).
 *
 * Передаётся в Init и SendClosingReceipt.
 */
final readonly class ReceiptDto
{
    /**
     * @param ReceiptItemDto[] $items
     */
    public function __construct(
        public TaxationEnum $taxation,
        public array $items,
        public ?string $email = null,
        public ?string $phone = null,
        public ?FfdVersionEnum $ffdVersion = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'Taxation' => $this->taxation->value,
            'Items'    => array_map(fn(ReceiptItemDto $i): array => $i->toArray(), $this->items),
        ];

        if ($this->email !== null) {
            $data['Email'] = $this->email;
        }
        if ($this->phone !== null) {
            $data['Phone'] = $this->phone;
        }
        if ($this->ffdVersion !== null) {
            $data['FfdVersion'] = $this->ffdVersion->value;
        }

        return $data;
    }
}
