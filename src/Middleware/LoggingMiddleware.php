<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;

/**
 * Logging middleware that wraps around a RequestAuthenticator to log API requests.
 * Sensitive headers like Authorization and Cookie are sanitized for security.
 */
final readonly class LoggingMiddleware implements RequestAuthenticatorInterface
{
    public function __construct(
        private RequestAuthenticatorInterface $inner,
        private LoggerInterface $logger,
    ) {
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $this->logger->debug('API Request', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'headers' => $this->sanitizeHeaders($request->getHeaders()),
        ]);

        return $this->inner->authenticate($request);
    }

    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;
        unset($sanitized['Authorization'], $sanitized['Cookie']);

        return $sanitized;
    }
}