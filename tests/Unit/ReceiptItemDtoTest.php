<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Shared\ReceiptItemDto;
use TBank\Payments\Enum\Fiscal\VatEnum;

final class ReceiptItemDtoTest extends TestCase
{
    public function testAcceptsValidItem(): void
    {
        $item = new ReceiptItemDto(
            name    : 'Товар',
            price   : 10000,
            quantity: 1.0,
            amount  : 10000,
            tax     : VatEnum::None,
        );

        $this->assertSame('Товар', $item->toArray()['Name']);
    }

    public function testAcceptsCyrillicNameOf128Characters(): void
    {
        $name = str_repeat('Я', 128);

        $item = new ReceiptItemDto(
            name    : $name,
            price   : 100,
            quantity: 1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );

        $this->assertSame(128, mb_strlen($item->name, 'UTF-8'));
        $this->assertSame(256, strlen($item->name));
    }

    public function testRejectsNameLongerThan128Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item name must not exceed 128 characters');

        new ReceiptItemDto(
            name    : str_repeat('А', 129),
            price   : 100,
            quantity: 1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );
    }

    public function testAcceptsNameExceeding128BytesButWithin128Characters(): void
    {
        // 100 кириллических символов = 200 байт в UTF-8 — strlen() отклонил бы при лимите 128 байт.
        $name = str_repeat('Ж', 100);

        $item = new ReceiptItemDto(
            name    : $name,
            price   : 100,
            quantity: 1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );

        $this->assertSame(100, mb_strlen($item->name, 'UTF-8'));
        $this->assertGreaterThan(128, strlen($item->name));
    }

    public function testRejectsInvalidUtf8Name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item name must be valid UTF-8');

        new ReceiptItemDto(
            name    : "\xFF\xFE invalid",
            price   : 100,
            quantity: 1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );
    }

    public function testRejectsNegativePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item price must not be negative');

        new ReceiptItemDto(
            name    : 'Товар',
            price   : -1,
            quantity: 1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );
    }

    public function testRejectsNegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item amount must not be negative');

        new ReceiptItemDto(
            name    : 'Товар',
            price   : 100,
            quantity: 1.0,
            amount  : -1,
            tax     : VatEnum::None,
        );
    }

    public function testAcceptsZeroPriceAndAmount(): void
    {
        $item = new ReceiptItemDto(
            name    : 'Подарок',
            price   : 0,
            quantity: 1.0,
            amount  : 0,
            tax     : VatEnum::None,
        );

        $this->assertSame(0, $item->price);
        $this->assertSame(0, $item->amount);
    }

    public function testRejectsZeroQuantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item quantity must be greater than zero');

        new ReceiptItemDto(
            name    : 'Товар',
            price   : 100,
            quantity: 0.0,
            amount  : 0,
            tax     : VatEnum::None,
        );
    }

    public function testRejectsNegativeQuantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Receipt item quantity must be greater than zero');

        new ReceiptItemDto(
            name    : 'Товар',
            price   : 100,
            quantity: -1.0,
            amount  : 100,
            tax     : VatEnum::None,
        );
    }
}
