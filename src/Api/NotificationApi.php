<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\ResendRequestDto;
use TBank\Payments\DTO\Response\ResendResponseDto;

/**
 * Повторная отправка webhook-уведомлений.
 *
 * @see https://developer.tbank.ru/eacq/api/resend-uvedomleniya
 */
final class NotificationApi extends BaseApi
{
    /**
     * Отправить неотправленные уведомления повторно.
     *
     * POST /v2/Resend
     */
    #[\NoDiscard]
    public function resend(?ResendRequestDto $request = null): ResendResponseDto
    {
        $data = $this->request('Resend', $request?->toArray() ?? []);

        return ResendResponseDto::fromArray($data);
    }
}
