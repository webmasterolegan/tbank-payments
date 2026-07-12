<?php

declare(strict_types=1);

namespace TBank\Payments\Api;

use TBank\Payments\DTO\Request\{AddCardRequestDto, RemoveCardRequestDto};
use TBank\Payments\DTO\Response\{AddCardResponseDto, CardListResponseDto, RemoveCardResponseDto};

/**
 * Привязка и управление картами.
 *
 * Методы:
 *  - AddCard      — инициирует привязку карты.
 *  - GetCardList  — возвращает список карт клиента.
 *  - RemoveCard   — удаляет привязанную карту.
 */
final class CardApi extends BaseApi
{
    /**
     * Инициировать привязку карты.
     *
     * POST /v2/AddCard
     */
    #[\NoDiscard]
    public function addCard(AddCardRequestDto $request): AddCardResponseDto
    {
        $data = $this->request('AddCard', $request->toArray());

        return AddCardResponseDto::fromArray($data);
    }

    /**
     * Получить список карт клиента.
     *
     * POST /v2/GetCardList
     *
     * @param string $customerKey Идентификатор клиента в системе мерчанта.
     */
    #[\NoDiscard]
    public function getCardList(string $customerKey): CardListResponseDto
    {
        $data = $this->request('GetCardList', ['CustomerKey' => $customerKey]);

        return CardListResponseDto::fromArray($data);
    }

    /**
     * Удалить привязанную карту.
     *
     * POST /v2/RemoveCard
     */
    #[\NoDiscard]
    public function removeCard(RemoveCardRequestDto $request): RemoveCardResponseDto
    {
        $data = $this->request('RemoveCard', $request->toArray());

        return RemoveCardResponseDto::fromArray($data);
    }
}
