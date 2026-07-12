<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Request\InitPaymentRequestDto;
use TBank\Payments\DTO\Shared\{ReceiptDto, ReceiptItemDto};
use TBank\Payments\Enum\Fiscal\{TaxationEnum, VatEnum};

final class InitPaymentRequestDtoTest extends TestCase
{
    public function testReceiptUsesApiFieldName(): void
    {
        $request = new InitPaymentRequestDto(
            amount : 10000,
            orderId: 'order-1',
            receipt: new ReceiptDto(
                taxation: TaxationEnum::UsnIncome,
                email   : 'buyer@example.com',
                items   : [
                    new ReceiptItemDto(
                        name    : 'Товар',
                        price   : 10000,
                        quantity: 1.0,
                        amount  : 10000,
                        tax     : VatEnum::None,
                    ),
                ],
            ),
        );

        $params = $request->toArray();

        $this->assertArrayHasKey('Receipt', $params);
        $this->assertArrayNotHasKey('ReceiptDto', $params);
    }
}
