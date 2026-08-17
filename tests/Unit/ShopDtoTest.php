<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Shared\ShopDto;

final class ShopDtoTest extends TestCase
{
    public function testSerializesRequiredFields(): void
    {
        $shop = new ShopDto(shopCode: '10001', amount: 150000);

        $this->assertSame([
            'ShopCode' => '10001',
            'Amount'   => 150000,
        ], $shop->toArray());
    }

    public function testSerializesFeeAsString(): void
    {
        $shop = new ShopDto(
            shopCode: '10001',
            amount  : 150000,
            name    : 'Футболка синяя',
            fee     : 2500,
        );

        $this->assertSame([
            'ShopCode' => '10001',
            'Amount'   => 150000,
            'Name'     => 'Футболка синяя',
            'Fee'      => '2500',
        ], $shop->toArray());
    }

    public function testOmitsOptionalFieldsWhenNull(): void
    {
        $shop = new ShopDto(shopCode: '10001', amount: 100);

        $this->assertArrayNotHasKey('Name', $shop->toArray());
        $this->assertArrayNotHasKey('Fee', $shop->toArray());
    }

    public function testAcceptsZeroAmountAndFee(): void
    {
        $shop = new ShopDto(shopCode: '10001', amount: 0, fee: 0);

        $this->assertSame(0, $shop->amount);
        $this->assertSame('0', $shop->toArray()['Fee']);
    }

    public function testRejectsEmptyShopCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop code must not be empty');

        new ShopDto(shopCode: '', amount: 100);
    }

    public function testRejectsNegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop amount must not be negative');

        new ShopDto(shopCode: '10001', amount: -1);
    }

    public function testRejectsNegativeFee(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop fee must not be negative');

        new ShopDto(shopCode: '10001', amount: 100, fee: -1);
    }

    public function testRejectsEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop name must not be empty');

        new ShopDto(shopCode: '10001', amount: 100, name: '');
    }

    public function testAcceptsCyrillicNameOf128Characters(): void
    {
        $name = str_repeat('Я', 128);

        $shop = new ShopDto(shopCode: '10001', amount: 100, name: $name);

        $this->assertSame(128, mb_strlen($shop->name ?? '', 'UTF-8'));
    }

    public function testRejectsNameLongerThan128Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop name must not exceed 128 characters');

        new ShopDto(shopCode: '10001', amount: 100, name: str_repeat('А', 129));
    }

    public function testRejectsInvalidUtf8Name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Shop name must be valid UTF-8');

        new ShopDto(shopCode: '10001', amount: 100, name: "\xFF\xFE invalid");
    }
}
