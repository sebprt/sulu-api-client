<?php

declare(strict_types=1);

namespace Sulu\ApiClient;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Sulu\ApiClient\Pagination\Page;
use Sulu\ApiClient\Pagination\Paginator;

final class ApiClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly SerializerInterface $serializer,
        private readonly RequestAuthenticatorInterface $authenticator,
        private readonly string $baseUrl,
    ) {
    }


    /**
     * Create an endpoint instance with the client dependencies wired.
     *
     * @template T of object
     * @param class-string<T> $endpointClass
     * @return T
     */
    public function createEndpoint(string $endpointClass): object
    {
        // All generated endpoints have the same constructor signature
        return new $endpointClass(
            $this->http,
            $this->requestFactory,
            $this->serializer,
            $this->authenticator,
            $this->baseUrl,
        );
    }

    /**
     * Factory to instantiate any generated endpoint class with the client dependencies wired.
     * @deprecated Use createEndpoint() instead. Will be removed in a future major version.
     *
     * @template T of object
     * @param class-string<T> $endpointClass
     * @return T
     */

    /**
     * Send the request defined by the given endpoint and return the parsed response.
     * Performs request(...) then parseResponse(...).
     */
    private function sendEndpointRequest(object $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        // duck-typing against generated endpoints API: request(...): ResponseInterface and parseResponse(ResponseInterface): mixed
        /** @var callable $request */
        $request = [$endpoint, 'request'];
        /** @var ResponseInterface $response */
        $response = $request($parameters, $query, $body);
        /** @var callable $parse */
        $parse = [$endpoint, 'parseResponse'];
        return $parse($response);
    }

    /**
     * CRUD-style helper: Create a resource using the given endpoint.
     * Semantics are provided by the chosen endpoint (typically POST).
     */
    public function create(object $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Read a resource (or collection) using the given endpoint.
     * Typically maps to GET endpoints; body is ignored.
     */
    public function read(object $endpoint, array $parameters = [], array $query = []): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, null);
    }

    /**
     * CRUD-style helper: Update a resource using the given endpoint.
     * Semantics are provided by the chosen endpoint (typically PATCH).
     */
    public function update(object $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Upsert a resource (create or replace) using the given endpoint.
     * Typically maps to PUT endpoints when supported by the API.
     */
    public function upsert(object $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Delete a resource using the given endpoint.
     * Typically maps to DELETE endpoints; body is ignored.
     */
    public function delete(object $endpoint, array $parameters = [], array $query = []): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, null);
    }

    /**
     * CRUD-style helper: List resources using a collection endpoint.
     * Provides a safer name than `list()` to avoid clashing with PHP language construct.
     */
    public function list(object $endpoint, array $parameters = [], array $query = [], ?string $embeddedKey = null, int $limit = 50): mixed
    {
        if ($embeddedKey !== null) {
            // Return a paginator that will iterate across all pages using the provided embedded key
            return $this->paginateEmbeddedCollection(
                $endpoint,
                embeddedKey: $embeddedKey,
                parameters: $parameters,
                baseQuery: $query,
                limit: $limit,
            );
        }

        // Fallback: single request (no pagination handling)
        return $this->sendEndpointRequest($endpoint, $parameters, $query, null);
    }


    /**
     * Build a Paginator around any endpoint that supports page/limit query parameters
     * and returns a payload with optional 'total' and collection under _embedded[$embeddedKey].
     *
     * @template T of array
     * @param object $endpoint the endpoint instance created via $this->createEndpoint(...)
     * @param string $embeddedKey key inside _embedded where items live, e.g. 'tags'
     * @param array<string,mixed> $parameters path/format parameters for the endpoint
     * @param array<string,mixed> $baseQuery base query to always pass (besides page & limit)
     * @return Paginator<T>
     */
    private function paginateEmbeddedCollection(object $endpoint, string $embeddedKey, array $parameters = [], array $baseQuery = [], int $limit = 50): Paginator
    {
        return new Paginator(
            limit: $limit,
            pageFetcher: function (int $page, int $limit) use ($endpoint, $embeddedKey, $parameters, $baseQuery): Page {
                /** @var callable $request */
                $request = [$endpoint, 'request'];
                /** @var ResponseInterface $response */
                $response = $request($parameters, array_merge($baseQuery, ['page' => $page, 'limit' => $limit]), null);
                /** @var callable $parse */
                $parse = [$endpoint, 'parseResponse'];
                $data = $parse($response);
                $items = [];
                $total = null;
                if (is_array($data)) {
                    $embedded = $data['_embedded'][$embeddedKey] ?? null;
                    if (is_array($embedded)) {
                        $items = array_values($embedded);
                    }
                    if (array_key_exists('total', $data) && $data['total'] !== null) {
                        $total = (int)$data['total'];
                    }
                }
                return new Page($items, $page, $limit, $total);
            }
        );
    }
}
