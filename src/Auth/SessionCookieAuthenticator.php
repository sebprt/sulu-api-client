<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * Authenticator that adds a Cookie header for session-based authentication.
 */
final class SessionCookieAuthenticator implements RequestAuthenticatorInterface
{
    /** @var array<string,string> */
    private array $cookies;

    /**
     * @param array<string,string>|string $cookies Either an associative array of cookies or a single cookie name
     * @param string|null $value Value for the single cookie if $cookies is a string
     */
    public function __construct(array|string $cookies, ?string $value = null)
    {
        if (is_string($cookies)) {
            $this->cookies = [$cookies => (string)($value ?? '')];
        } else {
            $this->cookies = $cookies;
        }
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if ($this->cookies === []) {
            return $request;
        }

        // Build cookie string
        $parts = [];
        foreach ($this->cookies as $name => $val) {
            if ($name === '') {
                continue;
            }
            $parts[] = rawurlencode((string)$name) . '=' . rawurlencode((string)$val);
        }
        if (empty($parts)) {
            return $request;
        }
        $cookieString = implode('; ', $parts);

        // Merge with existing Cookie header if present
        $existing = $request->getHeader('Cookie');
        if (!empty($existing)) {
            // Concatenate existing cookie header(s)
            $existingStr = implode('; ', $existing);
            // Avoid duplicate separators
            $cookieString = rtrim($existingStr, '; ') . '; ' . $cookieString;
        }

        return $request->withHeader('Cookie', $cookieString);
    }
}
