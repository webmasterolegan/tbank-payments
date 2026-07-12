<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\DTO\Response\CardListResponseDto;

final class CardListResponseDtoTest extends TestCase
{
    public function testFromArrayParsesCards(): void
    {
        $dto = CardListResponseDto::fromArray([
            'Success'   => true,
            'ErrorCode' => '0',
            'Cards'     => [
                [
                    'CardId'  => '1',
                    'Pan'     => '430000******0777',
                    'ExpDate' => '1225',
                    'Status'  => 'A',
                ],
            ],
        ]);

        $this->assertTrue($dto->success);
        $this->assertCount(1, $dto->cards);
        $this->assertSame('1', $dto->cards[0]->cardId);
        $this->assertSame('430000******0777', $dto->cards[0]->pan);
    }

    public function testFromArrayReturnsEmptyCardsOnFailure(): void
    {
        $dto = CardListResponseDto::fromArray([
            'Success'   => false,
            'ErrorCode' => '7',
            'Message'   => 'Not found',
            'Cards'     => [
                ['CardId' => '1', 'Pan' => 'x', 'ExpDate' => '0101'],
            ],
        ]);

        $this->assertFalse($dto->success);
        $this->assertSame([], $dto->cards);
    }
}
