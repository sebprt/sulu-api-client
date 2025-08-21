<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Endpoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface;
use Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher;
use Sulu\ApiClient\Exception\ConflictException;
use Sulu\ApiClient\Exception\ForbiddenException;
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\TooManyRequestsException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Serializer\JsonSerializer;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;
use Sulu\ApiClient\Tests\Fixtures\SimpleResponse;

abstract class EndpointTestCase extends TestCase
{
    protected MockObject&ClientInterface $httpClient;
    protected MockObject&RequestFactoryInterface $requestFactory;
    protected MockObject&RequestAuthenticatorInterface $authenticator;
    protected SerializerInterface $serializer;
    protected ContentTypeMatcherInterface $contentTypeMatcher;
    protected string $baseUrl = 'https://test.example.com';

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->authenticator = $this->createMock(RequestAuthenticatorInterface::class);
        $this->serializer = new JsonSerializer();
        $this->contentTypeMatcher = new DefaultContentTypeMatcher();
    }

    /**
     * Create an endpoint instance for testing.
     *
     * @template T of EndpointInterface
     * @param class-string<T> $endpointClass
     * @return T
     */
    protected function createEndpoint(string $endpointClass): EndpointInterface
    {
        return new $endpointClass(
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            $this->authenticator,
            $this->contentTypeMatcher,
            $this->baseUrl
        );
    }

    /**
     * Create a mock request for testing.
     */
    protected function createMockRequest(string $method = 'GET', string $uri = '/test'): RequestInterface
    {
        $request = new SimpleRequest($method, $uri);
        
        $this->requestFactory
            ->method('createRequest')
            ->willReturn($request);

        $this->authenticator
            ->method('authenticate')
            ->willReturnArgument(0);

        return $request;
    }

    /**
     * Create a mock response for testing.
     */
    protected function createMockResponse(
        int $statusCode = 200,
        string $body = '{"success": true}',
        array $headers = ['Content-Type' => 'application/json']
    ): ResponseInterface {
        return new SimpleResponse($statusCode, $headers, $body);
    }

    /**
     * Test generic HTTP error handling for all endpoints.
     *
     * @dataProvider httpErrorProvider
     */
    public function testHttpErrorHandling(int $status, string $expectedExceptionClass): void
    {
        $endpoint = $this->getEndpointForTesting();
        $request = $this->createMockRequest();
        $response = $this->createMockResponse($status, '{"error": "Test error"}');

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->expectException($expectedExceptionClass);

        // Use reflection to call parseResponse method
        $reflection = new \ReflectionClass($endpoint);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);
        $method->invoke($endpoint, $response);
    }

    /**
     * Data provider for HTTP error testing.
     *
     * @return array<array{int, class-string<\Throwable>}>
     */
    public function httpErrorProvider(): array
    {
        return [
            [400, ValidationException::class],
            [401, UnauthorizedException::class],
            [403, ForbiddenException::class],
            [404, NotFoundException::class],
            [409, ConflictException::class],
            [422, ValidationException::class],
            [429, TooManyRequestsException::class],
            [500, ServerErrorException::class],
        ];
    }

    /**
     * Test successful JSON response parsing.
     */
    public function testSuccessfulJsonResponseParsing(): void
    {
        $endpoint = $this->getEndpointForTesting();
        $expectedData = ['id' => 1, 'name' => 'Test'];
        $response = $this->createMockResponse(200, json_encode($expectedData));

        // Use reflection to call parseResponse method
        $reflection = new \ReflectionClass($endpoint);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);
        $result = $method->invoke($endpoint, $response);

        $this->assertEquals($expectedData, $result);
    }

    /**
     * Test 204 No Content response handling.
     */
    public function testNoContentResponse(): void
    {
        $endpoint = $this->getEndpointForTesting();
        $response = $this->createMockResponse(204, '');

        // Use reflection to call parseResponse method
        $reflection = new \ReflectionClass($endpoint);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);
        $result = $method->invoke($endpoint, $response);

        $this->assertNull($result);
    }

    /**
     * Get an endpoint instance for testing.
     * Must be implemented by concrete test classes.
     */
    abstract protected function getEndpointForTesting(): EndpointInterface;
}