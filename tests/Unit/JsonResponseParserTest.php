<?php

declare(strict_types=1);

namespace TBank\Payments\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TBank\Payments\Exceptions\{ApiException, NetworkException};
use TBank\Payments\Http\JsonResponseParser;

final class JsonResponseParserTest extends TestCase
{
    public function testParsesSuccessfulResponse(): void
    {
        $data = JsonResponseParser::parse(
            '{"Success":true,"PaymentId":"1"}',
            200,
            'https://securepay.tinkoff.ru/v2/GetState',
        );

        $this->assertTrue($data['Success']);
        $this->assertSame('1', $data['PaymentId']);
    }

    public function testThrowsApiExceptionWhenSuccessFalse(): void
    {
        try {
            JsonResponseParser::parse(
                '{"Success":false,"Message":"Declined","ErrorCode":"101"}',
                400,
            );
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame('Declined', $e->getMessage());
            $this->assertSame('101', $e->getErrorCode());
            $this->assertSame(400, $e->getHttpCode());
        }
    }

    public function testThrowsNetworkExceptionOnServerError(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('HTTP error [503]: Server error (URL: https://example.test/v2/Init)');

        JsonResponseParser::parse('{"Success":false}', 503, 'https://example.test/v2/Init');
    }

    public function testServerErrorIncludesApiMessage(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('HTTP error [500]: Temporary unavailable');

        JsonResponseParser::parse(
            '{"Success":false,"Message":"Temporary unavailable","ErrorCode":"99"}',
            500,
        );
    }

    public function testThrowsNetworkExceptionOnInvalidJson(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('Invalid JSON response (HTTP 200)');

        JsonResponseParser::parse('not-json', 200);
    }

    public function testInvalidJsonIncludesUrlWhenProvided(): void
    {
        $this->expectException(NetworkException::class);
        $this->expectExceptionMessage('(URL: https://example.test/v2/Init)');

        JsonResponseParser::parse('{', 200, 'https://example.test/v2/Init');
    }
}
