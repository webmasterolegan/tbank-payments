<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\AddCustomerRequestDto;
use TBank\Payments\DTO\Response\{AddCustomerResponseDto, GetCustomerResponseDto, RemoveCustomerResponseDto};

/**
 * Управление покупателями.
 *
 * @see https://developer.tbank.ru/eacq/api/add-customer
 */
final class CustomerApi extends BaseApi
{
    /**
     * Зарегистрировать покупателя.
     *
     * POST /v2/AddCustomer
     */
    #[\NoDiscard]
    public function add(AddCustomerRequestDto $request): AddCustomerResponseDto
    {
        $data = $this->request('AddCustomer', $request->toArray());

        return AddCustomerResponseDto::fromArray($data);
    }

    /**
     * Получить данные покупателя.
     *
     * POST /v2/GetCustomer
     */
    #[\NoDiscard]
    public function get(string $customerKey, ?string $ip = null): GetCustomerResponseDto
    {
        $params = ['CustomerKey' => $customerKey];

        if ($ip !== null) {
            $params['IP'] = $ip;
        }

        $data = $this->request('GetCustomer', $params);

        return GetCustomerResponseDto::fromArray($data);
    }

    /**
     * Удалить данные покупателя.
     *
     * POST /v2/RemoveCustomer
     *
     * @see https://developer.tbank.ru/eacq/api/remove-customer
     */
    #[\NoDiscard]
    public function remove(string $customerKey, ?string $ip = null): RemoveCustomerResponseDto
    {
        $params = ['CustomerKey' => $customerKey];

        if ($ip !== null) {
            $params['IP'] = $ip;
        }

        $data = $this->request('RemoveCustomer', $params);

        return RemoveCustomerResponseDto::fromArray($data);
    }
}
