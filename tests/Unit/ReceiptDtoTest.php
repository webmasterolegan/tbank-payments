<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Shared\{ReceiptDto, ReceiptItemDto};
use TBank\Payments\Enum\Fiscal\{PaymentMethodEnum, PaymentObjectEnum, TaxationEnum, VatEnum};

final class ReceiptDtoTest extends TestCase
{
    public function testReceiptToArrayUsesEnumValues(): void
    {
        $receipt = new ReceiptDto(
            taxation: TaxationEnum::UsnIncome,
            email   : 'buyer@example.com',
            items   : [
                new ReceiptItemDto(
                    name         : 'Товар',
                    price        : 10000,
                    quantity     : 1.0,
                    amount       : 10000,
                    tax          : VatEnum::None,
                    paymentObject: PaymentObjectEnum::Commodity,
                    paymentMethod: PaymentMethodEnum::FullPayment,
                ),
            ],
        );

        $array = $receipt->toArray();

        $this->assertSame('usn_income', $array['Taxation']);
        $this->assertSame('none', $array['Items'][0]['Tax']);
        $this->assertSame('commodity', $array['Items'][0]['PaymentObject']);
        $this->assertSame('full_payment', $array['Items'][0]['PaymentMethod']);
    }
}
