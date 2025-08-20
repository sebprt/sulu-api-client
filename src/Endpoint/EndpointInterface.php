<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

use Psr\Http\Message\ResponseInterface;

/**
 * Contract for API endpoints: build a request and parse its response.
 *
 * Implementations should be stateless and constructed with dependencies via DI
 * (PSR-18 client, PSR-17 factory, serializer, authenticator, base URL).
 */
interface EndpointInterface
{
    /**
     * Build, authenticate and send the HTTP request, returning the raw response.
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface;

    /**
     * Parse and map the raw response into domain data or throw a specific ApiException.
     */
    public function parseResponse(ResponseInterface $response): mixed;
}
