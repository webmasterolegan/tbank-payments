<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\{
    AddAccountQrRequestDto,
    ChargeQrRequestDto,
    GetQrBankListRequestDto,
    GetQrRequestDto,
};
use TBank\Payments\DTO\Response\{
    AddAccountQrResponseDto,
    ChargeQrResponseDto,
    GetAddAccountQrStateResponseDto,
    GetQrBankListResponseDto,
    GetQrResponseDto,
    GetQrStateResponseDto,
    QrMembersListResponseDto,
};

/**
 * Оплата через СБП.
 *
 * @see https://developer.tbank.ru/eacq/api/get-qr
 */
final class SbpApi extends BaseApi
{
    /**
     * Сформировать QR для СБП-платежа.
     *
     * POST /v2/GetQr
     *
     * Вызывается после Init для получения payload или SVG QR-кода.
     */
    #[\NoDiscard]
    public function getQr(GetQrRequestDto $request): GetQrResponseDto
    {
        $data = $this->request('GetQr', $request->toArray());

        return GetQrResponseDto::fromArray($data);
    }

    /**
     * Получить статус СБП-платежа или возврата.
     *
     * POST /v2/GetQrState
     *
     * @see https://developer.tbank.ru/eacq/api/get-qr-state
     */
    #[\NoDiscard]
    public function getQrState(string $paymentId): GetQrStateResponseDto
    {
        $data = $this->request('GetQrState', ['PaymentId' => $paymentId]);

        return GetQrStateResponseDto::fromArray($data);
    }

    /**
     * Получить список банков-участников СБП.
     *
     * POST /v2/GetQrBankList
     *
     * @see https://developer.tbank.ru/eacq/api/get-qr-bank-list
     */
    #[\NoDiscard]
    public function getQrBankList(GetQrBankListRequestDto $request): GetQrBankListResponseDto
    {
        $data = $this->request('GetQrBankList', $request->toArray());

        return GetQrBankListResponseDto::fromArray($data);
    }

    /**
     * Список участников для возврата QR-платежа.
     *
     * POST /v2/QrMembersList
     *
     * @see https://developer.tbank.ru/eacq/api/qr-members-list
     */
    #[\NoDiscard]
    public function qrMembersList(string $paymentId): QrMembersListResponseDto
    {
        $data = $this->request('QrMembersList', ['PaymentId' => $paymentId]);

        return QrMembersListResponseDto::fromArray($data);
    }

    /**
     * Списание по привязанному счёту СБП (автоплатёж).
     *
     * POST /v2/ChargeQr
     *
     * @see https://developer.tbank.ru/eacq/api/charge-qr
     */
    #[\NoDiscard]
    public function chargeQr(ChargeQrRequestDto $request): ChargeQrResponseDto
    {
        $data = $this->request('ChargeQr', $request->toArray());

        return ChargeQrResponseDto::fromArray($data);
    }

    /**
     * Привязать счёт покупателя к магазину через СБП.
     *
     * POST /v2/AddAccountQr
     *
     * @see https://developer.tbank.ru/eacq/api/add-account-qr
     */
    #[\NoDiscard]
    public function addAccountQr(AddAccountQrRequestDto $request): AddAccountQrResponseDto
    {
        $data = $this->request('AddAccountQr', $request->toArray());

        return AddAccountQrResponseDto::fromArray($data);
    }

    /**
     * Получить статус привязки счёта СБП.
     *
     * POST /v2/GetAddAccountQrState
     *
     * @see https://developer.tbank.ru/eacq/api/get-add-account-qr-state
     */
    #[\NoDiscard]
    public function getAddAccountQrState(string $requestKey): GetAddAccountQrStateResponseDto
    {
        $data = $this->request('GetAddAccountQrState', ['RequestKey' => $requestKey]);

        return GetAddAccountQrStateResponseDto::fromArray($data);
    }
}
