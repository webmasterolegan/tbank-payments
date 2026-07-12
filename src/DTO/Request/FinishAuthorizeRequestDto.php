<?php

declare(strict_types=1);

namespace TBank\Payments\DTO\Request;

/**
 * Запрос FinishAuthorize — передача карточных данных или результата 3DS.
 */
final readonly class FinishAuthorizeRequestDto extends BaseRequestDto
{
    public function __construct(
        public string $paymentId,
        /** Токен карты из платёжной формы (CardData) */
        public ?string $cardData = null,
        /** Зашифрованный CardData (RSA) */
        public ?string $encryptedPaymentData = null,
        /** MD из ответа ACS (после 3DS) */
        public ?string $md = null,
        /** PaRes из ответа ACS (после 3DS) */
        public ?string $paRes = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->filterNulls([
            'PaymentId'            => $this->paymentId,
            'CardData'             => $this->cardData,
            'EncryptedPaymentData' => $this->encryptedPaymentData,
            'MD'                   => $this->md,
            'PaRes'                => $this->paRes,
        ]);
    }
}
