<?php

declare(strict_types=1);

namespace TBank\Payments\Exceptions;

/** Неверная подпись входящего webhook-уведомления. */
final class InvalidWebhookSignatureException extends TBankException {}
