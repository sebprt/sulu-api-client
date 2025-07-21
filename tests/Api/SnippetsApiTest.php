<?php

namespace Sulu\ApiClient\Tests\Api;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\SnippetsApi;
use Sulu\ApiClient\SuluClient;

class SnippetsApiTest extends TestCase
{
    /**
     * @var SuluClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var SnippetsApi|\PHPUnit\Framework\MockObject\MockObject
     */
    private $api;

    protected function setUp(): void
    {
        $this->client = $this->createMock(SuluClient::class);

        // Create a partial mock of SnippetsApi to mock the protected methods
        $this->api = $this->getMockBuilder(SnippetsApi::class)
            ->setConstructorArgs([$this->client])
            ->onlyMethods(['get', 'post', 'put', 'delete', 'buildPath'])
            ->getMock();

        // Set up the buildPath method to return a predictable path
        $this->api->method('buildPath')
            ->willReturnCallback(function ($path, $parameters) {
                foreach ($parameters as $name => $value) {
                    $path = str_replace(sprintf('{%s}', $name), $value, $path);
                }
                return $path;
            });
    }

    public function testGetList()
    {
        $expectedResult = ['_embedded' => ['snippets' => []]];
        $parameters = ['locale' => 'en', 'limit' => 10];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/snippets', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getList($parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetByUuid()
    {
        $uuid = 'snippet-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'title' => 'Test Snippet'];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/snippets/' . $uuid, $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getByUuid($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testCreate()
    {
        $data = [
            'title' => 'New Snippet',
            'template' => 'default',
            'locale' => 'en'
        ];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => 'new-snippet-uuid'] + $data;

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/snippets', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->create($data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUpdate()
    {
        $uuid = 'snippet-uuid';
        $data = [
            'title' => 'Updated Snippet',
            'locale' => 'en'
        ];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid] + $data;

        $this->api->expects($this->once())
            ->method('put')
            ->with('/admin/api/snippets/' . $uuid, $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->update($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testDeleteSnippet()
    {
        $uuid = 'snippet-uuid';
        $parameters = ['locale' => 'en'];
        $body = [];
        $headers = [];
        $expectedResult = ['success' => true];

        $this->api->expects($this->once())
            ->method('delete')
            ->with('/admin/api/snippets/' . $uuid, $parameters, $body, $headers)
            ->willReturn($expectedResult);

        $result = $this->api->deleteSnippet($uuid, $parameters, $body, $headers);

        $this->assertSame($expectedResult, $result);
    }

    public function testPublish()
    {
        $uuid = 'snippet-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'published' => true];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/snippets/' . $uuid . '/publish', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->publish($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUnpublish()
    {
        $uuid = 'snippet-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'published' => false];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/snippets/' . $uuid . '/unpublish', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->unpublish($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetDefaults()
    {
        $parameters = ['locale' => 'en'];
        $expectedResult = ['defaults' => []];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/snippet-defaults', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getDefaults($parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetAreas()
    {
        $parameters = ['locale' => 'en'];
        $expectedResult = ['areas' => []];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/snippet-areas', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getAreas($parameters);

        $this->assertSame($expectedResult, $result);
    }
}
