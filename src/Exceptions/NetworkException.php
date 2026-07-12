<?php

declare(strict_types=1);

namespace TBank\Payments\Exceptions;

/** Ошибка сетевого уровня (cURL, DNS и т.д.). */
final class NetworkException extends TBankException {}
