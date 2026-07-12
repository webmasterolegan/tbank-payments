<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\CancelRequestDto;
use TBank\Payments\DTO\Response\CancelResponseDto;

/**
 * Отмена и возврат платежей.
 *
 * Методы:
 *  - Cancel — отмена/возврат как полный, так и частичный.
 */
final class RefundApi extends BaseApi
{
    /**
     * Отменить / вернуть платёж.
     *
     * POST /v2/Cancel
     *
     * Если Amount не передан — полная отмена/возврат.
     * Если Amount < суммы платежа — частичный возврат.
     *
     * @see https://developer.tbank.ru/eacq/api/otmena-platezha
     */
    #[\NoDiscard]
    public function cancel(CancelRequestDto $request): CancelResponseDto
    {
        $data = $this->request('Cancel', $request->toArray());

        return CancelResponseDto::fromArray($data);
    }
}
