<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Shared;

use TBank\Payments\Enum\DeviceTypeEnum;

/** Устройство плательщика (тип и ОС). */
final readonly class DeviceDto
{
    public function __construct(
        public DeviceTypeEnum $type,
        public string $os,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'Type' => $this->type->value,
            'Os'   => $this->os,
        ];
    }
}
