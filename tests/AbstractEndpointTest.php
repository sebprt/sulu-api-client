<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;
use Sulu\ApiClient\Endpoint\AbstractEndpoint;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Serializer\JsonSerializer;
use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;
use Sulu\ApiClient\Tests\Fixtures\SimpleResponse;

final class AbstractEndpointTest extends TestCase
{
    private function makeEndpoint(HttpClientInterface $http): AbstractEndpoint
    {
        $requestFactory = new class implements \Psr\Http\Message\RequestFactoryInterface {
            public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface { return new SimpleRequest($method, (string) $uri); }
        };
        $serializer = new JsonSerializer();
        $auth = new BearerTokenAuthenticator('test');

        return new class($http, $requestFactory, $serializer, $auth, 'https://example.test') extends AbstractEndpoint {
            protected const METHOD = 'POST';
            protected const PATH_TEMPLATE = '/admin/api/example.{_format}';
        };
    }

    public function testParseResponseDecodesJsonOnSuccess(): void
    {
        $http = m::mock(HttpClientInterface::class);
        $endpoint = $this->makeEndpoint($http);

        $response = new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode(['ok' => true], JSON_THROW_ON_ERROR));

        $result = $endpoint->parseResponse($response);

        self::assertIsArray($result);
        self::assertTrue($result['ok']);
    }

    public function testParseResponseThrowsValidationOn422(): void
    {
        $http = m::mock(HttpClientInterface::class);
        $endpoint = $this->makeEndpoint($http);

        $payload = ['errors' => ['name' => ['This value should not be blank.']]];
        $response = new SimpleResponse(422, ['Content-Type' => 'application/json'], json_encode($payload, JSON_THROW_ON_ERROR));

        $this->expectException(ValidationException::class);
        $endpoint->parseResponse($response);
    }

    public function testRequestBuildsAndSendsViaHttpClient(): void
    {
        $http = m::mock(HttpClientInterface::class);
        $endpoint = $this->makeEndpoint($http);

        $http->shouldReceive('sendRequest')
            ->once()
            ->andReturn(new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode(['ok' => true], JSON_THROW_ON_ERROR)));

        $resp = $endpoint->request(['_format' => 'json'], ['limit' => 10], ['name' => 'Foo']);
        self::assertInstanceOf(ResponseInterface::class, $resp);
        self::assertSame(200, $resp->getStatusCode());
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
