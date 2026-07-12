<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Response\{PaymentStateResponseDto, OrderStateResponseDto};

/**
 * Получение статуса платежа и заказа.
 *
 * @see https://developer.tbank.ru/eacq/api/status-platezha-ili-zakaza
 */
final class StatusApi extends BaseApi
{
    /**
     * Получить статус платежа.
     *
     * POST /v2/GetState
     *
     * @param string $paymentId PaymentId из ответа Init.
     */
    #[\NoDiscard]
    public function getState(string $paymentId): PaymentStateResponseDto
    {
        $data = $this->request('GetState', ['PaymentId' => $paymentId]);

        return PaymentStateResponseDto::fromArray($data);
    }

    /**
     * Получить статус заказа.
     *
     * POST /v2/CheckOrder
     *
     * @param string $orderId OrderId, переданный при инициации.
     */
    #[\NoDiscard]
    public function checkOrder(string $orderId): OrderStateResponseDto
    {
        $data = $this->request('CheckOrder', ['OrderId' => $orderId]);

        return OrderStateResponseDto::fromArray($data);
    }
}
