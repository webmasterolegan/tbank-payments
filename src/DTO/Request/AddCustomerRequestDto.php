<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/** Запрос AddCustomer — регистрация покупателя. */
final readonly class AddCustomerRequestDto
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
        $params = ['CustomerKey' => $this->customerKey];

        if ($this->ip !== null) {
            $params['IP'] = $this->ip;
        }
        if ($this->email !== null) {
            $params['Email'] = $this->email;
        }
        if ($this->phone !== null) {
            $params['Phone'] = $this->phone;
        }

        return $params;
    }
}
