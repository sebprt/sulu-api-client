<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Auth;

use Psr\Http\Message\RequestInterface;

final class BearerTokenAuthenticator implements RequestAuthenticatorInterface
{
    public function __construct(private readonly string $token)
    {
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Bearer ' . $this->token);
    }
}
