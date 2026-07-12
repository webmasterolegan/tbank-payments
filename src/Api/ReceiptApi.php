<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\SendReceiptRequestDto;
use TBank\Payments\DTO\Response\SendReceiptResponseDto;

/**
 * Методы работы с кассовыми чеками (ФФД 1.2).
 *
 * @see https://developer.tbank.ru/eacq/api/metodi-raboti-s-chekami
 */
final class ReceiptApi extends BaseApi
{
    /**
     * Отправить закрывающий чек.
     *
     * POST /v2/SendClosingReceipt
     */
    #[\NoDiscard]
    public function sendClosingReceipt(SendReceiptRequestDto $request): SendReceiptResponseDto
    {
        $data = $this->request('SendClosingReceipt', $request->toArray());

        return SendReceiptResponseDto::fromArray($data);
    }
}
