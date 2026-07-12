<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\TokenGenerator;

/**
 * Проверяем, что TokenGenerator реализует алгоритм из документации T-Bank.
 *
 * @see https://developer.tbank.ru/eacq/intro/developer/token
 */
final class TokenGeneratorTest extends TestCase
{
    private TokenGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new TokenGenerator('11111111111111');
    }

    public function testGeneratesCorrectTokenFromDocumentationExample(): void
    {
        // Пример из официальной документации
        $params = [
            'TerminalKey' => 'MerchantTerminalKey',
            'Amount'      => '19200',
            'OrderId'     => '00000',
            'Description' => 'Подарочная карта на 1000 рублей',
        ];

        $token = $this->generator->generate($params);

        // Ожидаемый хеш из документации
        $this->assertSame(
            '72dd466f8ace0a37a1f740ce5fb78101712bc0665d91a8108c7c8a0ccd426db2',
            $token,
        );
    }

    public function testSignAddsTokenToParams(): void
    {
        $params = ['TerminalKey' => 'key', 'Amount' => '100', 'OrderId' => 'o1'];
        $signed = $this->generator->sign($params);

        $this->assertArrayHasKey('Token', $signed);
        $this->assertSame(64, strlen($signed['Token'])); // SHA-256 hex = 64 chars
    }

    public function testSkipsNonScalarValues(): void
    {
        $params = [
            'TerminalKey' => 'key',
            'Amount'      => '100',
            'OrderId'     => 'o1',
            'Receipt'        => ['Email' => 'a@b.com'], // вложенный — исключается
            'DATA'        => ['Phone' => '+7'],       // вложенный — исключается
        ];

        // Не должно бросать исключение, вложенные поля просто игнорируются
        $token = $this->generator->generate($params);

        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token));
    }

    public function testTokenIsDeterministic(): void
    {
        $params = ['TerminalKey' => 'T', 'Amount' => '500', 'OrderId' => 'X'];

        $this->assertSame(
            $this->generator->generate($params),
            $this->generator->generate($params),
        );
    }

    public function testNormalizesBooleanValuesForToken(): void
    {
        $withBool = ['TerminalKey' => 'T', 'Success' => true];
        $withString = ['TerminalKey' => 'T', 'Success' => 'true'];

        $this->assertSame(
            $this->generator->generate($withBool),
            $this->generator->generate($withString),
        );
    }
}
