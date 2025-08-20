<?php

declare(strict_types=1);

namespace Sulu\ApiClient;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface;
use Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher;
use Sulu\ApiClient\Pagination\CursorPage;
use Sulu\ApiClient\Pagination\CursorPaginator;
use Sulu\ApiClient\Serializer\SerializerInterface;

final readonly class ApiClient
{
    public function __construct(
        private HttpClientInterface $http,
        private RequestFactoryInterface $requestFactory,
        private SerializerInterface $serializer,
        private RequestAuthenticatorInterface $authenticator,
        private string $baseUrl,
        private ContentTypeMatcherInterface $contentTypeMatcher = new DefaultContentTypeMatcher(),
    ) {
    }

    /**
     * Create an endpoint instance with the client dependencies wired.
     *
     * @template T of object
     *
     * @param class-string<T> $endpointClass
     *
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
            $this->contentTypeMatcher,
            $this->baseUrl,
        );
    }

    /**
     * Send the request defined by the given endpoint and return the parsed response.
     * Performs request(...) then parseResponse(...).
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    private function sendEndpointRequest(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        // duck-typing against generated endpoints API: request(...): ResponseInterface and parseResponse(ResponseInterface): mixed
        /** @var callable $request */
        $request = $endpoint->request(...);
        /** @var ResponseInterface $response */
        $response = $request($parameters, $query, $body);
        /** @var callable $parse */
        $parse = $endpoint->parseResponse(...);

        return $parse($response);
    }

    /**
     * CRUD-style helper: Create a resource using the given endpoint.
     * Semantics are provided by the chosen endpoint (typically POST).
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function create(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Read a resource (or collection) using the given endpoint.
     * Typically maps to GET endpoints; body is ignored.
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, null);
    }

    /**
     * CRUD-style helper: Update a resource using the given endpoint.
     * Semantics are provided by the chosen endpoint (typically PATCH).
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function update(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Upsert a resource (create or replace) using the given endpoint.
     * Typically maps to PUT endpoints when supported by the API.
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function upsert(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, $body);
    }

    /**
     * CRUD-style helper: Delete a resource using the given endpoint.
     * Typically maps to DELETE endpoints; body is ignored.
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function delete(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        return $this->sendEndpointRequest($endpoint, $parameters, $query, null);
    }

    /**
     * CRUD-style helper: List resources using a collection endpoint.
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function collection(EndpointInterface $endpoint, array $parameters = [], array $query = [], ?string $embeddedKey = null, int $limit = 50): mixed
    {
        if (null !== $embeddedKey) {
            // Return a cursor paginator that will iterate across all pages using the provided embedded key
            return $this->paginateEmbeddedCursorCollection(
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
     * Build a CursorPaginator around any endpoint that supports cursor/limit query parameters
     * and returns a payload with the collection under _embedded[$embeddedKey] and a next cursor
     * at either top-level 'nextCursor', under _links.next.cursor, or within _links.next.href query.
     *
     * @param EndpointInterface   $endpoint      the endpoint instance created via $this->createEndpoint(...)
     * @param string              $embeddedKey   key inside _embedded where items live, e.g. 'tags'
     * @param array<string,mixed> $parameters    path/format parameters for the endpoint
     * @param array<string,mixed> $baseQuery     base query to always pass (besides cursor & limit)
     * @param string|null         $initialCursor starting cursor, or null to start from beginning
     *
     * @return CursorPaginator<array<string,mixed>>
     */
    public function paginateEmbeddedCursorCollection(EndpointInterface $endpoint, string $embeddedKey, array $parameters = [], array $baseQuery = [], int $limit = 50, ?string $initialCursor = null): CursorPaginator
    {
        return new CursorPaginator(
            limit: $limit,
            /* @return CursorPage<array<string,mixed>> */
            pageFetcher: function (?string $cursor, int $limit) use ($endpoint, $embeddedKey, $parameters, $baseQuery): CursorPage {
                /** @var callable $request */
                $request = $endpoint->request(...);
                $query = array_merge($baseQuery, ['limit' => $limit]);
                if (null !== $cursor) {
                    $query['cursor'] = $cursor;
                }
                /** @var ResponseInterface $response */
                $response = $request($parameters, $query, null);
                /** @var callable $parse */
                $parse = $endpoint->parseResponse(...);
                $data = $parse($response);

                $items = [];
                $nextCursor = null;
                if (is_array($data)) {
                    $embedded = $data['_embedded'][$embeddedKey] ?? null;
                    if (is_array($embedded)) {
                        $items = array_values($embedded);
                    }
                    // Preferred: top-level nextCursor
                    if (isset($data['nextCursor']) && is_string($data['nextCursor']) && '' !== $data['nextCursor']) {
                        $nextCursor = $data['nextCursor'];
                    } elseif (isset($data['_links']['next']['cursor']) && is_string($data['_links']['next']['cursor']) && '' !== $data['_links']['next']['cursor']) {
                        $nextCursor = $data['_links']['next']['cursor'];
                    } elseif (isset($data['_links']['next']['href']) && is_string($data['_links']['next']['href'])) {
                        // Try to parse cursor query parameter from href
                        $href = $data['_links']['next']['href'];
                        $parts = parse_url($href);
                        if (isset($parts['query'])) {
                            parse_str($parts['query'], $q);
                            if (isset($q['cursor']) && is_string($q['cursor']) && '' !== $q['cursor']) {
                                $nextCursor = $q['cursor'];
                            }
                        }
                    }
                }

                return new CursorPage($items, $nextCursor);
            },
            initialCursor: $initialCursor,
        );
    }
}
