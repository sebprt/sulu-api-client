<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests;

use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;
use Sulu\ApiClient\Tests\Fixtures\SimpleResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\ApiClient;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Serializer\SerializerInterface;

final class ApiClientTest extends TestCase
{
    private function psr17(): RequestFactoryInterface
    {
        return new class implements RequestFactoryInterface {
            public function createRequest(string $method, $uri): RequestInterface
            { return new SimpleRequest($method, (string) $uri); }
        };
    }

    private function serializer(): SerializerInterface
    {
        return new class implements SerializerInterface {
            public function serialize(mixed $data, string $format = 'json'): string { return json_encode($data) ?: ''; }
            public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed { return json_decode($payload, true); }
        };
    }

    private function auth(): RequestAuthenticatorInterface
    {
        return new class implements RequestAuthenticatorInterface {
            public function authenticate(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\RequestInterface
            { return $request; }
        };
    }

    public function testCreateEndpointInstantiatesClass(): void
    {
        $http = new class implements ClientInterface { public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); } };
        $client = new ApiClient($http, $this->psr17(), $this->serializer(), $this->auth(), 'https://api.test');

        $endpoint = $client->createEndpoint(\Sulu\ApiClient\Endpoint\SuluDeleteAccountEndpoint::class);

        self::assertInstanceOf(\Sulu\ApiClient\Endpoint\SuluDeleteAccountEndpoint::class, $endpoint);
    }

    public function testCrudHelpersDelegateToEndpoint(): void
    {
        $http = new class implements ClientInterface { public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200, ['Content-Type' => 'application/json'], '{}'); } };
        $client = new ApiClient($http, $this->psr17(), $this->serializer(), $this->auth(), 'https://api.test');

        $calls = [];
        $endpoint = new class($calls) implements EndpointInterface {
            public function __construct(private array &$calls) {}
            public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface { $this->calls[] = ['request', $parameters, $query, $body]; return new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode(['ok' => true])); }
            public function parseResponse(ResponseInterface $response): mixed { $this->calls[] = ['parse']; return json_decode((string) $response->getBody(), true); }
        };

        $client->create($endpoint, ['a' => 1], ['b' => 2], ['c' => 3]);
        $client->read($endpoint, ['a' => 1], ['b' => 2]);
        $client->update($endpoint, ['a' => 1], ['b' => 2], ['c' => 3]);
        $client->upsert($endpoint, ['a' => 1], ['b' => 2], ['c' => 3]);
        $client->delete($endpoint, ['a' => 1], ['b' => 2]);

        self::assertNotEmpty($calls);
        // Ensure parseResponse was called multiple times
        $parseCalls = array_filter($calls, fn($c) => $c[0] === 'parse');
        self::assertGreaterThanOrEqual(5, count($parseCalls));
    }

    public function testListWithEmbeddedPaginatorAndNextCursorExtraction(): void
    {
        $http = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        };
        $client = new ApiClient($http, $this->psr17(), $this->serializer(), $this->auth(), 'https://api.test');

        // endpoint that will return pages in three forms
        $endpoint = new class implements EndpointInterface {
            private int $i = 0;
            public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
            {
                $this->i++;
                if (1 === $this->i) {
                    $data = [ '_embedded' => ['items' => [1]], '_links' => ['next' => ['cursor' => 'c2']]];
                } elseif (2 === $this->i) {
                    $data = [ '_embedded' => ['items' => [2]], '_links' => ['next' => ['href' => 'https://api.test/items?cursor=c3']] ];
                } else {
                    $data = [ '_embedded' => ['items' => [3]], 'nextCursor' => null ];
                }
                return new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode($data));
            }
            public function parseResponse(ResponseInterface $response): mixed { return json_decode((string) $response->getBody(), true); }
        };

        $paginator = $client->list($endpoint, [], [], embeddedKey: 'items', limit: 2);
        $items = iterator_to_array($paginator);

        self::assertSame([1, 2, 3], $items);
    }
}
