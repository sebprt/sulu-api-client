<?php

namespace Sulu\ApiClient\Tests\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\AbstractApi;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\SuluClient;

class AbstractApiTest extends TestCase
{
    /**
     * @var SuluClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var HttpClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $httpClient;

    /**
     * @var AbstractApi
     */
    private $api;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);

        $this->client = $this->createMock(SuluClient::class);
        $this->client->method('getHttpClient')->willReturn($this->httpClient);

        $this->api = new ConcreteApi($this->client);
    }

    public function testGet()
    {
        $expectedData = ['foo' => 'bar'];
        $response = new Response(200, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', '/admin/test', ['query' => ['param' => 'value']])
            ->willReturn($response);

        $result = $this->api->testGet('/test', ['param' => 'value']);

        $this->assertEquals($expectedData, $result);
    }

    public function testPost()
    {
        $expectedData = ['id' => 123];
        $response = new Response(201, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('POST', '/admin/test', [
                'query' => ['param' => 'value'],
                'json' => ['name' => 'Test']
            ])
            ->willReturn($response);

        $result = $this->api->testPost('/test', ['param' => 'value'], ['name' => 'Test']);

        $this->assertEquals($expectedData, $result);
    }

    public function testPut()
    {
        $expectedData = ['id' => 123, 'name' => 'Updated'];
        $response = new Response(200, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('PUT', '/admin/test/123', [
                'query' => ['param' => 'value'],
                'json' => ['name' => 'Updated']
            ])
            ->willReturn($response);

        $result = $this->api->testPut('/test/123', ['param' => 'value'], ['name' => 'Updated']);

        $this->assertEquals($expectedData, $result);
    }

    public function testDelete()
    {
        $expectedData = ['success' => true];
        $response = new Response(200, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('DELETE', '/admin/test/123', ['query' => ['param' => 'value']])
            ->willReturn($response);

        $result = $this->api->testDelete('/test/123', ['param' => 'value']);

        $this->assertEquals($expectedData, $result);
    }

    public function testPatch()
    {
        $expectedData = ['id' => 123, 'name' => 'Patched'];
        $response = new Response(200, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('PATCH', '/admin/test/123', [
                'query' => ['param' => 'value'],
                'json' => ['name' => 'Patched']
            ])
            ->willReturn($response);

        $result = $this->api->testPatch('/test/123', ['param' => 'value'], ['name' => 'Patched']);

        $this->assertEquals($expectedData, $result);
    }

    public function testRequestWithHeaders()
    {
        $expectedData = ['foo' => 'bar'];
        $response = new Response(200, [], json_encode($expectedData));

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', '/admin/test', [
                'headers' => ['X-Custom' => 'Value']
            ])
            ->willReturn($response);

        $result = $this->api->testRequest('GET', '/test', [], [], ['X-Custom' => 'Value']);

        $this->assertEquals($expectedData, $result);
    }

    public function testBuildPath()
    {
        $path = $this->api->testBuildPath('/api/{resource}/{id}', [
            'resource' => 'pages',
            'id' => '123'
        ]);

        $this->assertEquals('/api/pages/123', $path);
    }
}

/**
 * Concrete implementation of AbstractApi for testing
 */
class ConcreteApi extends AbstractApi
{
    public function testGet($path, array $parameters = [], array $headers = []): array
    {
        return $this->get($path, $parameters, $headers);
    }

    public function testPost($path, array $parameters = [], array $body = [], array $headers = []): array
    {
        return $this->post($path, $parameters, $body, $headers);
    }

    public function testPut($path, array $parameters = [], array $body = [], array $headers = []): array
    {
        return $this->put($path, $parameters, $body, $headers);
    }

    public function testDelete($path, array $parameters = [], array $body = [], array $headers = []): array
    {
        return $this->delete($path, $parameters, $body, $headers);
    }

    public function testPatch($path, array $parameters = [], array $body = [], array $headers = []): array
    {
        return $this->patch($path, $parameters, $body, $headers);
    }

    public function testRequest($method, $path, array $parameters = [], array $body = [], array $headers = []): array
    {
        return $this->request($method, $path, $parameters, $body, $headers);
    }

    public function testBuildPath($path, array $parameters = []): string
    {
        return $this->buildPath($path, $parameters);
    }
}
