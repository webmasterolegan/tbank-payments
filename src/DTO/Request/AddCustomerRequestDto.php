<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос AddCustomer — регистрация покупателя. */
final readonly class AddCustomerRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $customerKey,
        public ?string $ip = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'CustomerKey' => $this->customerKey,
            'IP'          => $this->ip,
            'Email'       => $this->email,
            'Phone'       => $this->phone,
        ]);
    }
}
