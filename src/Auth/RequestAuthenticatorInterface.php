<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * Applies authentication to an outgoing PSR-7 request.
 */
interface RequestAuthenticatorInterface
{
    public function authenticate(RequestInterface $request): RequestInterface;
}
