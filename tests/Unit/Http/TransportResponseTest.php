<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Http;

use Hyperplural\Ohmywishes\Exception\ApiException;
use Hyperplural\Ohmywishes\Http\TransportResponse;
use PHPUnit\Framework\TestCase;

final class TransportResponseTest extends TestCase
{
    public function testEmptyBodyReturnsNullJson(): void
    {
        $response = new TransportResponse(204, [], '');

        self::assertNull($response->json());
    }

    public function testInvalidJsonThrowsException(): void
    {
        $response = new TransportResponse(200, [], '{invalid json');

        $this->expectException(ApiException::class);
        $response->json();
    }
}
