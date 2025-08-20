<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\ApiClient;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Sulu\ApiClient\Tests\Fixtures\SimpleResponse;

final class CursorApiClientTest extends TestCase
{
    public function testPaginateEmbeddedCursorCollectionIteratesAllItems(): void
    {
        $psr17 = new class () implements \Psr\Http\Message\RequestFactoryInterface {
                    public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface
                    {
                        throw new \RuntimeException('RequestFactory not used in this test');
                    }
                };
        $http = new class () implements \Psr\Http\Client\ClientInterface {
            public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                throw new \RuntimeException('HTTP client should not be used in this test');
            }
        };
        $serializer = new class () implements SerializerInterface {
            public function serialize(mixed $data, string $format = 'json'): string
            {
                return json_encode($data) ?: '';
            }

            public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed
            {
                return json_decode($payload, true);
            }
        };
        $auth = new class () implements RequestAuthenticatorInterface {
            public function authenticate(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\RequestInterface
            {
                return $request;
            }
        };

        $client = new ApiClient($http, $psr17, $serializer, $auth, 'https://example.test');

        // A simple duck-typed endpoint with request/parseResponse methods
        $endpoint = new class () implements \Sulu\ApiClient\Endpoint\EndpointInterface { 
            private array $pages = [
                null => [
                    '_embedded' => ['items' => [1, 2]],
                    'nextCursor' => 'c2',
                ],
                'c2' => [
                    '_embedded' => ['items' => [3]],
                    'nextCursor' => null,
                ],
            ];

            public function request(array $parameters = [], array $query = [], mixed $body = null): \Psr\Http\Message\ResponseInterface
            {
                $cursor = $query['cursor'] ?? null;
                $data = $this->pages[$cursor] ?? ['_embedded' => ['items' => []], 'nextCursor' => null];

                return new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode($data));
            }

            public function parseResponse(\Psr\Http\Message\ResponseInterface $response): mixed
            {
                return json_decode((string) $response->getBody(), true);
            }
        };

        $paginator = $client->paginateEmbeddedCursorCollection(
            $endpoint,
            embeddedKey: 'items',
            parameters: ['_format' => 'json'],
            baseQuery: [],
            limit: 2,
            initialCursor: null,
        );

        $items = iterator_to_array($paginator);

        self::assertSame([1, 2, 3], $items);
    }
}
