<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Support\ApiUrlBuilder;

final class ApiUrlBuilderTest extends TestCase
{
    public function testJoinsEndpointToBaseUrl(): void
    {
        $url = ApiUrlBuilder::join('https://securepay.tinkoff.ru/v2', 'GetQr');

        $this->assertSame('https://securepay.tinkoff.ru/v2/GetQr', $url);
    }

    public function testTrimsSlashes(): void
    {
        $url = ApiUrlBuilder::join('https://securepay.tinkoff.ru/v2/', '/Init');

        $this->assertSame('https://securepay.tinkoff.ru/v2/Init', $url);
    }

    public function testThrowsOnInvalidBaseUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ApiUrlBuilder::join('not a valid uri with spaces', 'Init');
    }
}
