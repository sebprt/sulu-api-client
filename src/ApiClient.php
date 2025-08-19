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

    public function getHttpClient(): HttpClientInterface
    {
        return $this->http;
    }

    /**
     * Factory to instantiate any generated endpoint class with the client dependencies wired.
     *
     * @template T of object
     * @param class-string<T> $endpointClass
     * @return T
     */
    public function endpoint(string $endpointClass): object
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
     * Execute a generated endpoint by performing request(...) then parseResponse(...).
     * This provides a simple, typed one-liner in userland code.
     */
    public function executeEndpoint(object $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
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
     * Build a Paginator around any endpoint that supports page/limit query parameters
     * and returns a payload with optional 'total' and collection under _embedded[$embeddedKey].
     *
     * @template T of array
     * @param object $endpoint the endpoint instance created via $this->endpoint(...)
     * @param string $embeddedKey key inside _embedded where items live, e.g. 'tags'
     * @param array<string,mixed> $parameters path/format parameters for the endpoint
     * @param array<string,mixed> $baseQuery base query to always pass (besides page & limit)
     * @return Paginator<T>
     */
    public function paginateEndpoint(object $endpoint, string $embeddedKey, array $parameters = [], array $baseQuery = [], int $limit = 50): Paginator
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

    /**
     * Convenience to wrap a custom pageFetcher into a Paginator.
     *
     * @param callable(int $page, int $limit): Page $pageFetcher
     */
    public function paginate(int $limit, callable $pageFetcher): Paginator
    {
        return new Paginator($limit, $pageFetcher);
    }

    /**
     * @return array<string,mixed>|list<mixed>|null
     */
    private function handleJsonResponse(ResponseInterface $response): array|null
    {
        $status = $response->getStatusCode();
        $body = (string)$response->getBody();
        $data = $body !== '' ? $this->serializer->deserialize($body, 'json') : null;

        if ($status >= 200 && $status < 300) {
            return is_array($data) ? $data : null;
        }

        if ($status === 404) {
            throw new NotFoundException('Resource not found', 404);
        }
        if ($status === 422 || $status === 400) {
            $errors = is_array($data) ? $data : null;
            throw new ValidationException('Validation error', $status, null, $errors);
        }

        $message = is_array($data) && isset($data['message']) ? (string)$data['message'] : 'API Error';
        throw new ApiException($message, $status);
    }
}
