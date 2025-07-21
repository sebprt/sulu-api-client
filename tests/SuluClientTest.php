<?php

namespace Sulu\ApiClient\Tests;

use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Api\PagesApi;
use Sulu\ApiClient\Api\MediaApi;
use Sulu\ApiClient\Api\CategoriesApi;
use Sulu\ApiClient\Api\TagsApi;
use Sulu\ApiClient\Api\ContactsApi;
use Sulu\ApiClient\Api\AccountsApi;
use Sulu\ApiClient\Api\WebspacesApi;
use Sulu\ApiClient\Api\LanguagesApi;
use Sulu\ApiClient\Api\UserApi;
use Sulu\ApiClient\Api\RolesApi;
use Sulu\ApiClient\Api\PermissionsApi;
use Sulu\ApiClient\Http\ClientOptions;
use Sulu\ApiClient\SuluClient;

class SuluClientTest extends TestCase
{
    private $baseUrl = 'https://example.com';
    private $sessionId = 'test-session-id';

    public function testConstructor()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertEquals($this->baseUrl, $client->getBaseUrl());
    }

    public function testConstructorWithOptions()
    {
        $options = new ClientOptions();
        $options->setTimeout(60);
        $options->setVerifySsl(false);

        $client = new SuluClient($this->baseUrl, $this->sessionId, $options);

        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertEquals($this->baseUrl, $client->getBaseUrl());

        // Note: We can't easily test that the options were applied to the HttpClient
        // since we don't have access to its internal configuration
    }

    public function testBaseUrlTrailingSlash()
    {
        $client = new SuluClient($this->baseUrl . '/', $this->sessionId);

        $this->assertEquals($this->baseUrl, $client->getBaseUrl());
    }

    public function testPagesApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(PagesApi::class, $client->pages());

        // Test that the same instance is returned on subsequent calls
        $this->assertSame($client->pages(), $client->pages());
    }

    public function testMediaApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(MediaApi::class, $client->media());
        $this->assertSame($client->media(), $client->media());
    }

    public function testCategoriesApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(CategoriesApi::class, $client->categories());
        $this->assertSame($client->categories(), $client->categories());
    }

    public function testTagsApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(TagsApi::class, $client->tags());
        $this->assertSame($client->tags(), $client->tags());
    }

    public function testContactsApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(ContactsApi::class, $client->contacts());
        $this->assertSame($client->contacts(), $client->contacts());
    }

    public function testAccountsApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(AccountsApi::class, $client->accounts());
        $this->assertSame($client->accounts(), $client->accounts());
    }

    public function testWebspacesApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(WebspacesApi::class, $client->webspaces());
        $this->assertSame($client->webspaces(), $client->webspaces());
    }

    public function testLanguagesApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(LanguagesApi::class, $client->languages());
        $this->assertSame($client->languages(), $client->languages());
    }

    public function testUserApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(UserApi::class, $client->user());
        $this->assertSame($client->user(), $client->user());
    }

    public function testRolesApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(RolesApi::class, $client->roles());
        $this->assertSame($client->roles(), $client->roles());
    }

    public function testPermissionsApi()
    {
        $client = new SuluClient($this->baseUrl, $this->sessionId);

        $this->assertInstanceOf(PermissionsApi::class, $client->permissions());
        $this->assertSame($client->permissions(), $client->permissions());
    }

}
