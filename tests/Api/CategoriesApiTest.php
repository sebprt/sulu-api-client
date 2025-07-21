<?php

namespace Sulu\ApiClient\Tests\Api;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\CategoriesApi;
use Sulu\ApiClient\SuluClient;

class CategoriesApiTest extends TestCase
{
    /**
     * @var SuluClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var CategoriesApi|\PHPUnit\Framework\MockObject\MockObject
     */
    private $api;

    protected function setUp(): void
    {
        $this->client = $this->createMock(SuluClient::class);

        // Create a partial mock of CategoriesApi to mock the protected methods
        $this->api = $this->getMockBuilder(CategoriesApi::class)
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
        $expectedResult = ['_embedded' => ['categories' => []]];
        $parameters = [];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/categories', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getList($parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGet()
    {
        $uuid = 'category-uuid';
        $parameters = [];
        $expectedResult = ['id' => $uuid, 'name' => 'Test Category'];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/categories/' . $uuid, $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getByUuid($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testCreate()
    {
        $data = [
            'name' => 'New Category',
            'key' => 'new-category'
        ];
        $parameters = [];
        $expectedResult = ['id' => 'new-category-uuid'] + $data;

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/categories', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->create($data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUpdate()
    {
        $uuid = 'category-uuid';
        $data = [
            'name' => 'Updated Category'
        ];
        $parameters = [];
        $expectedResult = ['id' => $uuid] + $data;

        $this->api->expects($this->once())
            ->method('put')
            ->with('/admin/api/categories/' . $uuid, $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->update($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testDeleteCategory()
    {
        $uuid = 'category-uuid';
        $parameters = [];
        $body = [];
        $headers = [];
        $expectedResult = ['success' => true];

        $this->api->expects($this->once())
            ->method('delete')
            ->with('/admin/api/categories/' . $uuid, $parameters, $body, $headers)
            ->willReturn($expectedResult);

        $result = $this->api->deleteCategory($uuid, $parameters, $body, $headers);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetChildren()
    {
        $uuid = 'category-uuid';
        $parameters = [];
        $expectedResult = ['_embedded' => ['categories' => []]];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/categories/' . $uuid . '/children', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getChildren($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }
}
