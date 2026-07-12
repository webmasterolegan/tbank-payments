<?php

declare(strict_types=1);

namespace TBank\Payments\Exceptions;

/** Невалидное тело входящего webhook-уведомления (например, битый JSON). */
final class InvalidWebhookPayloadException extends TBankException {}
