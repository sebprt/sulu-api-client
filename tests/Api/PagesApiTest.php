<?php

namespace Sulu\ApiClient\Tests\Api;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\PagesApi;
use Sulu\ApiClient\SuluClient;

class PagesApiTest extends TestCase
{
    /**
     * @var SuluClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var PagesApi|\PHPUnit\Framework\MockObject\MockObject
     */
    private $api;

    protected function setUp(): void
    {
        $this->client = $this->createMock(SuluClient::class);

        // Create a partial mock of PagesApi to mock the protected methods
        $this->api = $this->getMockBuilder(PagesApi::class)
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
        $expectedResult = ['_embedded' => ['pages' => []]];
        $parameters = ['locale' => 'en', 'limit' => 10];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/pages', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getList($parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGet()
    {
        $uuid = 'page-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'title' => 'Test Page'];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/pages/' . $uuid, $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getByUuid($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testCreate()
    {
        $data = [
            'title' => 'New Page',
            'template' => 'default',
            'parent' => 'parent-uuid',
            'locale' => 'en'
        ];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => 'new-page-uuid'] + $data;

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/pages', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->create($data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUpdate()
    {
        $uuid = 'page-uuid';
        $data = [
            'title' => 'Updated Page',
            'locale' => 'en'
        ];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid] + $data;

        $this->api->expects($this->once())
            ->method('put')
            ->with('/admin/api/pages/' . $uuid, $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->update($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testDeletePage()
    {
        $uuid = 'page-uuid';
        $parameters = ['locale' => 'en'];
        $body = [];
        $headers = [];
        $expectedResult = ['success' => true];

        $this->api->expects($this->once())
            ->method('delete')
            ->with('/admin/api/pages/' . $uuid, $parameters, $body, $headers)
            ->willReturn($expectedResult);

        $result = $this->api->deletePage($uuid, $parameters, $body, $headers);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetChildren()
    {
        $uuid = 'page-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['_embedded' => ['pages' => []]];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/pages/' . $uuid . '/children', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getChildren($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testCopy()
    {
        $uuid = 'page-uuid';
        $data = ['destination' => 'destination-uuid'];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => 'copied-page-uuid'];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/pages/' . $uuid . '/copy', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->copy($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testMove()
    {
        $uuid = 'page-uuid';
        $data = ['destination' => 'destination-uuid'];
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/pages/' . $uuid . '/move', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->move($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testPublish()
    {
        $uuid = 'page-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'published' => true];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/pages/' . $uuid . '/publish', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->publish($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUnpublish()
    {
        $uuid = 'page-uuid';
        $parameters = ['locale' => 'en'];
        $expectedResult = ['id' => $uuid, 'published' => false];

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/pages/' . $uuid . '/unpublish', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->unpublish($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }
}
