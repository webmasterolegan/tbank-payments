<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\{ChargeRequestDto, InitPaymentRequestDto, FinishAuthorizeRequestDto, ConfirmRequestDto};
use TBank\Payments\DTO\Response\{ChargeResponseDto, InitPaymentResponseDto, FinishAuthorizeResponseDto, ConfirmResponseDto};

/**
 * Инициирование, авторизация и подтверждение платежей.
 *
 * Методы:
 *  - Init          — создаёт платёж и возвращает PaymentURL.
 *  - FinishAuthorize — завершает авторизацию (3DS / без).
 *  - Confirm       — подтверждает двухстадийное списание.
 *  - Charge        — списание по RebillId (COF).
 */
final class PaymentApi extends BaseApi
{
    /**
     * Инициировать платёж.
     *
     * POST /v2/Init
     *
     * @see https://developer.tbank.ru/eacq/api/init
     */
    #[\NoDiscard]
    public function init(InitPaymentRequestDto $request): InitPaymentResponseDto
    {
        $data = $this->request('Init', $request->toArray());

        return InitPaymentResponseDto::fromArray($data);
    }

    /**
     * Подтвердить платёж (передача данных карты / результата 3DS).
     *
     * POST /v2/FinishAuthorize
     *
     * @see https://developer.tbank.ru/eacq/api/finish-authorize
     */
    #[\NoDiscard]
    public function finishAuthorize(FinishAuthorizeRequestDto $request): FinishAuthorizeResponseDto
    {
        $data = $this->request('FinishAuthorize', $request->toArray());

        return FinishAuthorizeResponseDto::fromArray($data);
    }

    /**
     * Подтвердить списание (двухстадийный платёж).
     *
     * POST /v2/Confirm
     *
     * @see https://developer.tbank.ru/eacq/api/confirm
     */
    #[\NoDiscard]
    public function confirm(ConfirmRequestDto $request): ConfirmResponseDto
    {
        $data = $this->request('Confirm', $request->toArray());

        return ConfirmResponseDto::fromArray($data);
    }

    /**
     * Списать по сохранённым реквизитам (RebillId).
     *
     * POST /v2/Charge
     *
     * @see https://developer.tbank.ru/eacq/api/charge
     */
    #[\NoDiscard]
    public function charge(ChargeRequestDto $request): ChargeResponseDto
    {
        $data = $this->request('Charge', $request->toArray());

        return ChargeResponseDto::fromArray($data);
    }
}
