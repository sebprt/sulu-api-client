<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Auth;

use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;
use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;

final class BearerTokenAuthenticatorTest extends TestCase
{
    public function testAuthenticateAddsAuthorizationHeader(): void
    {
        $auth = new BearerTokenAuthenticator('abc123');
        $req = new SimpleRequest('GET', 'https://example.test');

        $out = $auth->authenticate($req);

        self::assertSame('Bearer abc123', $out->getHeaderLine('Authorization'));
        // Ensure immutability of original request
        self::assertSame('', $req->getHeaderLine('Authorization'));
    }
}
