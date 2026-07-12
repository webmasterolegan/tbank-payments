<?php

declare(strict_types=1);

namespace TBank\Payments\Enum;

/** Тип данных в ответе GetQr (СБП). */
enum QrDataTypeEnum: string
{
    /** Платёжная ссылка (payload). */
    case Payload = 'PAYLOAD';

    /** SVG-изображение QR-кода. */
    case Image = 'IMAGE';
}
