<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Hyperplural\Ohmywishes\Http\GuzzleTransport;
use PHPUnit\Framework\TestCase;

final class GuzzleTransportTest extends TestCase
{
    public function testItSendsJsonRequestsAndNormalizesResponseHeaders(): void
    {
        $history = [];
        $client = $this->createClient($history, new Response(200, ['X-Test' => 'alpha'], '{"ok":true}'));
        $transport = new GuzzleTransport('https://ohmywishes.com', client: $client);

        $response = $transport->request('POST', '/api/v3/auth/email/login', [], [
            'email' => 'test@example.net',
            'confirmationCode' => '1234',
        ]);

        self::assertSame(200, $response->statusCode);
        self::assertSame(['alpha'], $response->headers['x-test']);
        self::assertSame('{"ok":true}', $response->body);
        self::assertCount(1, $history);
        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/api/v3/auth/email/login', $history[0]['request']->getUri()->getPath());
        self::assertSame('application/json', $history[0]['request']->getHeaderLine('Content-Type'));
        self::assertSame('{"email":"test@example.net","confirmationCode":"1234"}', (string) $history[0]['request']->getBody());
    }

    public function testItSendsMultipartRequestsWithoutForcingJsonHeaders(): void
    {
        $history = [];
        $client = $this->createClient($history, new Response(200, [], '{"ok":true}'));
        $transport = new GuzzleTransport('https://ohmywishes.com', client: $client);

        $response = $transport->request('POST', '/api/v2/users/self/avatars', [], null, [], [[
            'name' => 'picture',
            'contents' => 'avatar-bytes',
            'filename' => 'avatar.png',
        ]]);

        self::assertSame(200, $response->statusCode);
        self::assertCount(1, $history);
        self::assertSame('POST', $history[0]['request']->getMethod());
        self::assertSame('/api/v2/users/self/avatars', $history[0]['request']->getUri()->getPath());
        self::assertStringStartsWith('multipart/form-data; boundary=', $history[0]['request']->getHeaderLine('Content-Type'));
        self::assertStringContainsString('name="picture"', (string) $history[0]['request']->getBody());
        self::assertStringContainsString('filename="avatar.png"', (string) $history[0]['request']->getBody());
    }

    /**
     * @param array<int, array{request:\Psr\Http\Message\RequestInterface, options: array<string, mixed>}> $history
     */
    private function createClient(array &$history, Response $response): Client
    {
        $mock = new MockHandler([$response]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        return new Client([
            'handler' => $stack,
            'http_errors' => false,
        ]);
    }
}
