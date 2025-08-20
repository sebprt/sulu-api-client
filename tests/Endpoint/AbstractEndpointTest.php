<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Endpoint;

use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;
use Sulu\ApiClient\Tests\Fixtures\SimpleResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;
use Sulu\ApiClient\Endpoint\AbstractEndpoint;
use Sulu\ApiClient\Exception\ForbiddenException;
use Sulu\ApiClient\Exception\InvalidJsonException;
use Sulu\ApiClient\Exception\RedirectionException;
use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\TooManyRequestsException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Exception\UnexpectedResponseException;
use Sulu\ApiClient\Exception\UnsupportedMediaTypeException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher;

final class AbstractEndpointTest extends TestCase
{
    private function serializer(): SerializerInterface
    {
        return new class () implements SerializerInterface {
            public function serialize(mixed $data, string $format = 'json'): string
            { return json_encode($data) ?: ''; }
            public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed
            {
                return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            }
        };
    }

    private function dummyEndpoint(ClientInterface $http): AbstractEndpoint
    {
        $psr17 = new class implements \Psr\Http\Message\RequestFactoryInterface { public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface { return new SimpleRequest($method, (string) $uri); } };
        $serializer = $this->serializer();
        $auth = new BearerTokenAuthenticator('t0ken');

        return new class ($http, $psr17, $serializer, $auth, new DefaultContentTypeMatcher(), 'https://api.test') extends AbstractEndpoint {
            protected const METHOD = 'POST';
            protected const PATH_TEMPLATE = '/a/{id}.{_format}';
        };
    }

    public function testParseResponseSuccessJson(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });

        $resp = new SimpleResponse(200, ['Content-Type' => 'application/json'], json_encode(['x' => 1]));
        $out = $endpoint->parseResponse($resp);
        self::assertSame(['x' => 1], $out);
    }

    public function testParseResponseInvalidJsonThrows(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });

        $resp = new SimpleResponse(200, ['Content-Type' => 'application/json'], '{invalid');
        $this->expectException(InvalidJsonException::class);
        try {
            $endpoint->parseResponse($resp);
        } catch (InvalidJsonException $e) {
            self::assertSame(200, $e->getCode());
            throw $e;
        }
    }

    public function testParseResponse204ReturnsNull(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });
        $resp = new SimpleResponse(204, ['Content-Type' => 'application/json'], '');
        self::assertNull($endpoint->parseResponse($resp));
    }

    public function testParseResponseNonJsonSuccessReturnsBodyString(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });
        $resp = new SimpleResponse(200, ['Content-Type' => 'text/plain'], 'hello');
        self::assertSame('hello', $endpoint->parseResponse($resp));
    }

    public function testClientErrorMappings(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });
        $data = ['title' => 'Bad Request', 'detail' => 'Invalid input'];
        $this->expectException(ValidationException::class);
        $endpoint->parseResponse(new SimpleResponse(400, ['Content-Type' => 'application/json'], json_encode($data)));
    }

    public function testSpecificErrorMappings(): void
    {
        $endpoint = $this->dummyEndpoint(new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }
        });

        $this->expectException(UnauthorizedException::class);
        try { $endpoint->parseResponse(new SimpleResponse(401, ['Content-Type' => 'application/json'], json_encode(['title' => 'Unauthorized']))); } catch (UnauthorizedException $e) {}

        $this->expectException(ForbiddenException::class);
        try { $endpoint->parseResponse(new SimpleResponse(403, ['Content-Type' => 'application/json'], json_encode(['title' => 'Forbidden']))); } catch (ForbiddenException $e) {}

        $this->expectException(UnsupportedMediaTypeException::class);
        try { $endpoint->parseResponse(new SimpleResponse(415, ['Content-Type' => 'application/json'], json_encode(['title' => 'Unsupported']))); } catch (UnsupportedMediaTypeException $e) {}

        $this->expectException(TooManyRequestsException::class);
        try { $endpoint->parseResponse(new SimpleResponse(429, ['Content-Type' => 'application/json', 'Retry-After' => '120'], json_encode(['title' => 'Slow down']))); } catch (TooManyRequestsException $e) {
            self::assertStringContainsString('retry after 120', $e->getMessage());
        }

        $this->expectException(RedirectionException::class);
        try { $endpoint->parseResponse(new SimpleResponse(302, ['Content-Type' => 'application/json'], json_encode(['title' => 'Moved']))); } catch (RedirectionException $e) {}

        $this->expectException(ServerErrorException::class);
        try { $endpoint->parseResponse(new SimpleResponse(500, ['Content-Type' => 'application/json'], json_encode(['message' => 'boom']))); } catch (ServerErrorException $e) {
            self::assertSame(500, $e->getCode());
        }

        $this->expectException(UnexpectedResponseException::class);
        $endpoint->parseResponse(new SimpleResponse(418, ['Content-Type' => 'application/json'], json_encode(['message' => 'teapot'])));
    }

    public function testRequestBuildsUrlAppliesAuthAndBody(): void
    {
        $captured = null;
        $http = new class($captured) implements ClientInterface {
            public function __construct(private mixed &$captured) {}
            public function sendRequest(RequestInterface $request): ResponseInterface
            { $this->captured = $request; return new SimpleResponse(200, ['Content-Type' => 'application/json'], '{}'); }
        };
        $endpoint = $this->dummyEndpoint($http);

        $resp = $endpoint->request(parameters: ['id' => 123, '_format' => 'json'], query: ['q' => 'x'], body: ['a' => 1]);
        self::assertSame(200, $resp->getStatusCode());

        // Validate request built
        $request = (new \ReflectionProperty($http, 'captured'))->getValue($http);
        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://api.test/a/123.json?q=x', (string) $request->getUri());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('Bearer t0ken', $request->getHeaderLine('Authorization'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame(json_encode(['a' => 1]), (string) $request->getBody());
    }

    public function testGetMethodIgnoresBodyAndContentType(): void
    {
        // Endpoint that uses GET
        $endpoint = new class (
            new class implements ClientInterface { public function sendRequest(RequestInterface $request): ResponseInterface { return new SimpleResponse(200); }},
            new class implements \Psr\Http\Message\RequestFactoryInterface { public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface { return new SimpleRequest($method, (string) $uri); } },
            $this->serializer(),
            new BearerTokenAuthenticator('t'),
            new DefaultContentTypeMatcher(),
            'https://api.test'
        ) extends AbstractEndpoint {
            protected const METHOD = 'GET';
            protected const PATH_TEMPLATE = '/b/{name}';
        };

        // Build request with a body, ensure it is ignored
        $endpoint->request(parameters: ['name' => 'joe'], query: [], body: ['ignored' => true]);
        // We cannot capture body easily here; reuse capturing
        $captured = null;
        $http = new class($captured) implements ClientInterface {
            public function __construct(private mixed &$captured) {}
            public function sendRequest(RequestInterface $request): ResponseInterface
            { $this->captured = $request; return new SimpleResponse(200); }
        };
        $endpoint2 = new class (
            $http,
            new class implements \Psr\Http\Message\RequestFactoryInterface { public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface { return new SimpleRequest($method, (string) $uri); } },
            $this->serializer(),
            new BearerTokenAuthenticator('t'),
            new DefaultContentTypeMatcher(),
            'https://api.test'
        ) extends AbstractEndpoint {
            protected const METHOD = 'GET';
            protected const PATH_TEMPLATE = '/b/{name}';
        };
        $endpoint2->request(parameters: ['name' => 'joe'], query: ['x' => '1'], body: ['ignored' => true]);
        $request = (new \ReflectionProperty($http, 'captured'))->getValue($http);
        self::assertSame('', $request->getHeaderLine('Content-Type'));
        self::assertSame('', (string) $request->getBody());
        self::assertSame('https://api.test/b/joe?x=1', (string) $request->getUri());
    }
}
