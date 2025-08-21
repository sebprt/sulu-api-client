<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Endpoint;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Endpoint\Factory\EndpointFactory;
use Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface;
use Sulu\ApiClient\Serializer\SerializerInterface;

class EndpointFactoryTest extends TestCase
{
    private EndpointFactory $factory;
    private HttpClientInterface $http;
    private RequestFactoryInterface $requestFactory;
    private SerializerInterface $serializer;
    private RequestAuthenticatorInterface $authenticator;
    private ContentTypeMatcherInterface $contentTypeMatcher;
    private string $baseUrl;

    protected function setUp(): void
    {
        $this->http = $this->createMock(HttpClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->authenticator = $this->createMock(RequestAuthenticatorInterface::class);
        $this->contentTypeMatcher = $this->createMock(ContentTypeMatcherInterface::class);
        $this->baseUrl = 'https://api.example.com';

        $this->factory = new EndpointFactory(
            $this->http,
            $this->requestFactory,
            $this->serializer,
            $this->authenticator,
            $this->contentTypeMatcher,
            $this->baseUrl,
        );
    }

    public function testCreateValidEndpoint(): void
    {
        $endpoint = $this->factory->create(TestEndpoint::class);

        $this->assertInstanceOf(TestEndpoint::class, $endpoint);
        $this->assertInstanceOf(EndpointInterface::class, $endpoint);
    }

    public function testCreateInvalidEndpointThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class stdClass must implement EndpointInterface');

        $this->factory->create(\stdClass::class);
    }

    public function testCreateNonExistentClassThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->create('NonExistentClass');
    }
}

// Test endpoint class for testing purposes
class TestEndpoint implements EndpointInterface
{
    public function __construct(
        HttpClientInterface $http,
        RequestFactoryInterface $requestFactory,
        SerializerInterface $serializer,
        RequestAuthenticatorInterface $authenticator,
        ContentTypeMatcherInterface $contentTypeMatcher,
        string $baseUrl,
    ) {
        // Constructor matches the expected signature
    }

    public function request(array $parameters = [], array $query = [], mixed $body = null): \Psr\Http\Message\ResponseInterface
    {
        // Mock implementation for testing
        return $this->createMock(\Psr\Http\Message\ResponseInterface::class);
    }

    public function parseResponse(\Psr\Http\Message\ResponseInterface $response): mixed
    {
        // Mock implementation for testing
        return [];
    }
}