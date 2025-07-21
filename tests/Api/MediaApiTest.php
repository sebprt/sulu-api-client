<?php

namespace Sulu\ApiClient\Tests\Api;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\MediaApi;
use Sulu\ApiClient\SuluClient;

class MediaApiTest extends TestCase
{
    /**
     * @var SuluClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var MediaApi|\PHPUnit\Framework\MockObject\MockObject
     */
    private $api;

    protected function setUp(): void
    {
        $this->client = $this->createMock(SuluClient::class);
        $this->client->method('getBaseUrl')->willReturn('https://example.com');

        // Create a partial mock of MediaApi to mock the protected methods
        $this->api = $this->getMockBuilder(MediaApi::class)
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
        $expectedResult = ['_embedded' => ['media' => []]];
        $parameters = ['limit' => 10, 'page' => 1];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/media', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getList($parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGet()
    {
        $uuid = 'media-uuid';
        $parameters = [];
        $expectedResult = ['id' => $uuid, 'title' => 'Test Media'];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/media/' . $uuid, $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getByUuid($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testCreate()
    {
        $data = [
            'title' => 'New Media',
            'collection' => 'collection-uuid'
        ];
        $parameters = [];
        $expectedResult = ['id' => 'new-media-uuid'] + $data;

        $this->api->expects($this->once())
            ->method('post')
            ->with('/admin/api/media', $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->create($data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testUpdate()
    {
        $uuid = 'media-uuid';
        $data = [
            'title' => 'Updated Media'
        ];
        $parameters = [];
        $expectedResult = ['id' => $uuid] + $data;

        $this->api->expects($this->once())
            ->method('put')
            ->with('/admin/api/media/' . $uuid, $parameters, $data)
            ->willReturn($expectedResult);

        $result = $this->api->update($uuid, $data, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testDeleteMedia()
    {
        $uuid = 'media-uuid';
        $parameters = [];
        $body = [];
        $headers = [];
        $expectedResult = ['success' => true];

        $this->api->expects($this->once())
            ->method('delete')
            ->with('/admin/api/media/' . $uuid, $parameters, $body, $headers)
            ->willReturn($expectedResult);

        $result = $this->api->deleteMedia($uuid, $parameters, $body, $headers);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetFormats()
    {
        $uuid = 'media-uuid';
        $parameters = [];
        $expectedResult = ['formats' => ['sulu-small' => ['url' => '/media/123/sulu-small']]];

        $this->api->expects($this->once())
            ->method('get')
            ->with('/admin/api/media/' . $uuid . '/formats', $parameters)
            ->willReturn($expectedResult);

        $result = $this->api->getFormats($uuid, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetPreviewUrl()
    {
        $uuid = 'media-uuid';
        $format = 'sulu-small';
        $expectedUrl = 'https://example.com/media/' . $uuid . '/' . $format;

        $result = $this->api->getPreviewUrl($uuid, $format);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetPreviewUrlWithDefaultFormat()
    {
        $uuid = 'media-uuid';
        $expectedUrl = 'https://example.com/media/' . $uuid . '/sulu-small';

        $result = $this->api->getPreviewUrl($uuid);

        $this->assertEquals($expectedUrl, $result);
    }
}
