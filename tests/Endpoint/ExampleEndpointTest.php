<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Endpoint;

use Psr\Http\Message\RequestInterface;
use Sulu\ApiClient\Endpoint\AbstractEndpoint;
use Sulu\ApiClient\Endpoint\EndpointInterface;

/**
 * Example test demonstrating the usage of EndpointTestCase.
 * This serves as a template for testing concrete endpoint implementations.
 */
class ExampleEndpointTest extends EndpointTestCase
{
    /**
     * Create a simple endpoint for testing purposes.
     */
    protected function getEndpointForTesting(): EndpointInterface
    {
        return new class(
            $this->httpClient,
            $this->requestFactory,
            $this->serializer,
            $this->authenticator,
            $this->contentTypeMatcher,
            $this->baseUrl
        ) extends AbstractEndpoint {
            public function testMethod(): mixed
            {
                $request = $this->createRequest('GET', '/test');
                $response = $this->sendRequest($request);
                return $this->parseResponse($response);
            }

            protected function createRequest(string $method, string $path, mixed $body = null): RequestInterface
            {
                $request = $this->requestFactory->createRequest($method, $this->baseUrl . $path);
                return $this->authenticator->authenticate($request);
            }
        };
    }

    /**
     * Test that the endpoint can be created successfully.
     */
    public function testEndpointCreation(): void
    {
        $endpoint = $this->getEndpointForTesting();
        $this->assertInstanceOf(EndpointInterface::class, $endpoint);
        $this->assertInstanceOf(AbstractEndpoint::class, $endpoint);
    }

    /**
     * Test that the generic test methods work correctly.
     * The actual HTTP error handling tests are inherited from EndpointTestCase.
     */
    public function testInheritedTestsWork(): void
    {
        // This test verifies that the data provider works
        $provider = $this->httpErrorProvider();
        $this->assertIsArray($provider);
        $this->assertNotEmpty($provider);
        
        // Check that each provider item has the expected structure
        foreach ($provider as $item) {
            $this->assertIsArray($item);
            $this->assertCount(2, $item);
            $this->assertIsInt($item[0]); // HTTP status code
            $this->assertIsString($item[1]); // Exception class name
            $this->assertTrue(class_exists($item[1])); // Exception class exists
        }
    }
}