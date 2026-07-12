<?php

declare(strict_types=1);

/**
 * Пример: привязка, список и удаление карт.
 *
 * Запуск:
 *   php examples/05-cards.php bind <CustomerKey>
 *   php examples/05-cards.php list <CustomerKey>
 *   php examples/05-cards.php remove <CustomerKey> <CardId>
 */

require __DIR__ . '/bootstrap.php';

use TBank\Payments\DTO\Request\{AddCardRequestDto, RemoveCardRequestDto};
use TBank\Payments\Enum\CardCheckTypeEnum;
use TBank\Payments\Exceptions\{ApiException, NetworkException};

$action      = $argv[1] ?? 'list';
$customerKey = $argv[2] ?? 'user-42';
$cardId      = $argv[3] ?? null;

$client = createClient();

try {
    match ($action) {
        'bind' => bindCard($client, $customerKey),
        'list' => listCards($client, $customerKey),
        'remove' => removeCard($client, $customerKey, $cardId),
        default => throw new InvalidArgumentException("Unknown action: {$action}"),
    };
} catch (ApiException $e) {
    fwrite(STDERR, "API error [{$e->getErrorCode()}]: {$e->getMessage()}\n");
    exit(1);
} catch (NetworkException $e) {
    fwrite(STDERR, "Network error: {$e->getMessage()}\n");
    exit(1);
}

function bindCard(\TBank\Payments\TBankClient $client, string $customerKey): void
{
    $result = $client->card()->addCard(
        new AddCardRequestDto(
            customerKey    : $customerKey,
            checkType      : CardCheckTypeEnum::Hold,
            notificationUrl: 'https://myshop.ru/api/tbank/webhook',
            successUrl     : 'https://myshop.ru/cards/success',
            failUrl        : 'https://myshop.ru/cards/fail',
        ),
    );

    echo "Привязка инициирована\n";
    echo "RequestKey: {$result->requestKey}\n";

    if ($result->paymentUrl !== null) {
        echo "PaymentURL: {$result->paymentUrl}\n";
    }
}

function listCards(\TBank\Payments\TBankClient $client, string $customerKey): void
{
    $result = $client->card()->getCardList($customerKey);

    if ($result->cards === []) {
        echo "У клиента {$customerKey} нет привязанных карт\n";
        return;
    }

    foreach ($result->cards as $card) {
        echo "- {$card->cardId}: {$card->pan}, exp {$card->expDate}\n";
    }
}

function removeCard(\TBank\Payments\TBankClient $client, string $customerKey, ?string $cardId): void
{
    if ($cardId === null) {
        throw new InvalidArgumentException('CardId required for remove action');
    }

    $result = $client->card()->removeCard(
        new RemoveCardRequestDto(
            customerKey: $customerKey,
            cardId     : $cardId,
        ),
    );

    echo "Карта удалена, статус: {$result->status}\n";
}
