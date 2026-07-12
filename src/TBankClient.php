<?php

declare(strict_types=1);

namespace TBank\Payments;

use TBank\Payments\Api\{
    CardApi,
    CustomerApi,
    NotificationApi,
    PaymentApi,
    ReceiptApi,
    RefundApi,
    SbpApi,
    StatusApi,
};
use TBank\Payments\Enum\EnvironmentEnum;
use TBank\Payments\Http\{HttpClient, HttpClientContract, RetryingHttpClient};

/**
 * Главный клиент для работы с API T-Bank интернет-эквайринга.
 *
 * @see https://developer.tbank.ru/eacq/api/priem-platezhei
 */
final class TBankClient
{
    private readonly HttpClientContract $httpClient;
    private readonly TokenGenerator $tokenGenerator;

    private ?PaymentApi $payment = null;
    private ?CardApi $card = null;
    private ?RefundApi $refund = null;
    private ?StatusApi $status = null;
    private ?ReceiptApi $receipt = null;
    private ?SbpApi $sbp = null;
    private ?CustomerApi $customer = null;
    private ?NotificationApi $notifications = null;

    public function __construct(
        private readonly string $terminalKey,
        private readonly string $password,
        EnvironmentEnum|string $environment = EnvironmentEnum::Production,
        private readonly int $timeout = 30,
        ?HttpClientContract $httpClient = null,
        int $retryAttempts = 0,
        int $retryDelayMs = 200,
        int $connectTimeout = 10,
        bool $reuseConnection = false,
    ) {
        $baseUrl = $environment instanceof EnvironmentEnum
            ? $environment->baseUrl()
            : $environment;

        $httpClient = $httpClient ?? new HttpClient(
            baseUrl         : $baseUrl,
            timeout         : $this->timeout,
            connectTimeout  : $connectTimeout,
            reuseConnection : $reuseConnection,
        );

        if ($retryAttempts > 0 && !$httpClient instanceof RetryingHttpClient) {
            $httpClient = new RetryingHttpClient($httpClient, $retryAttempts, $retryDelayMs);
        }

        $this->httpClient     = $httpClient;
        $this->tokenGenerator = new TokenGenerator($this->password);
    }

    public function payment(): PaymentApi
    {
        return $this->payment ??= new PaymentApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function card(): CardApi
    {
        return $this->card ??= new CardApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function refund(): RefundApi
    {
        return $this->refund ??= new RefundApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function status(): StatusApi
    {
        return $this->status ??= new StatusApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function receipt(): ReceiptApi
    {
        return $this->receipt ??= new ReceiptApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function sbp(): SbpApi
    {
        return $this->sbp ??= new SbpApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function customer(): CustomerApi
    {
        return $this->customer ??= new CustomerApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    public function notifications(): NotificationApi
    {
        return $this->notifications ??= new NotificationApi(
            $this->httpClient,
            $this->tokenGenerator,
            $this->terminalKey,
        );
    }

    /** Обработчик webhook с учётными данными текущего терминала. */
    #[\NoDiscard]
    public function webhookHandler(): WebhookHandler
    {
        return new WebhookHandler(
            tokenGenerator     : $this->tokenGenerator,
            expectedTerminalKey: $this->terminalKey,
        );
    }
}
