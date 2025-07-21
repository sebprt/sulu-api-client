<?php

namespace Sulu\ApiClient;

use GuzzleHttp\Client as HttpClient;
use Sulu\ApiClient\Api\PagesApi;
use Sulu\ApiClient\Api\MediaApi;
use Sulu\ApiClient\Api\CategoriesApi;
use Sulu\ApiClient\Api\TagsApi;
use Sulu\ApiClient\Api\ContactsApi;
use Sulu\ApiClient\Api\AccountsApi;
use Sulu\ApiClient\Api\SnippetsApi;
use Sulu\ApiClient\Api\WebspacesApi;
use Sulu\ApiClient\Api\LanguagesApi;
use Sulu\ApiClient\Api\UserApi;
use Sulu\ApiClient\Api\RolesApi;
use Sulu\ApiClient\Api\PermissionsApi;
use Sulu\ApiClient\Http\ClientOptions;

/**
 * Main client class for interacting with the Sulu API.
 */
class SuluClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var array
     */
    private $apiInstances = [];

    /**
     * Create a new Sulu API client instance.
     *
     * @param string $baseUrl The base URL of the Sulu instance
     * @param string $sessionId The session ID for cookie-based authentication (SULUSESSID)
     * @param ClientOptions|null $options Additional client options
     */
    public function __construct($baseUrl, $sessionId, $options = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $sessionId;

        $options = $options ? $options : new ClientOptions();

        $clientConfig = [
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Cookie' => 'SULUSESSID=' . $this->apiKey,
            ],
            'timeout' => $options->getTimeout(),
            'verify' => $options->getVerifySsl(),
            'cookies' => true,
        ];

        $this->httpClient = new HttpClient($clientConfig);
    }

    /**
     * Get the pages API client.
     *
     * @return PagesApi
     */
    public function pages()
    {
        return $this->getApiInstance(PagesApi::class);
    }

    /**
     * Get the media API client.
     *
     * @return MediaApi
     */
    public function media()
    {
        return $this->getApiInstance(MediaApi::class);
    }

    /**
     * Get the categories API client.
     *
     * @return CategoriesApi
     */
    public function categories()
    {
        return $this->getApiInstance(CategoriesApi::class);
    }

    /**
     * Get the tags API client.
     *
     * @return TagsApi
     */
    public function tags()
    {
        return $this->getApiInstance(TagsApi::class);
    }

    /**
     * Get the contacts API client.
     *
     * @return ContactsApi
     */
    public function contacts()
    {
        return $this->getApiInstance(ContactsApi::class);
    }

    /**
     * Get the accounts API client.
     *
     * @return AccountsApi
     */
    public function accounts()
    {
        return $this->getApiInstance(AccountsApi::class);
    }

    /**
     * Get the snippets API client.
     *
     * @return SnippetsApi
     */
    public function snippets()
    {
        return $this->getApiInstance(SnippetsApi::class);
    }

    /**
     * Get the webspaces API client.
     *
     * @return WebspacesApi
     */
    public function webspaces()
    {
        return $this->getApiInstance(WebspacesApi::class);
    }

    /**
     * Get the languages API client.
     *
     * @return LanguagesApi
     */
    public function languages()
    {
        return $this->getApiInstance(LanguagesApi::class);
    }

    /**
     * Get the user API client.
     *
     * @return UserApi
     */
    public function user()
    {
        return $this->getApiInstance(UserApi::class);
    }

    /**
     * Get the roles API client.
     *
     * @return RolesApi
     */
    public function roles()
    {
        return $this->getApiInstance(RolesApi::class);
    }

    /**
     * Get the permissions API client.
     *
     * @return PermissionsApi
     */
    public function permissions()
    {
        return $this->getApiInstance(PermissionsApi::class);
    }

    /**
     * Get the HTTP client instance.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Get the base URL of the Sulu instance.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Get an API instance by class name.
     *
     * @param string $className
     * @return mixed
     */
    private function getApiInstance($className)
    {
        if (!isset($this->apiInstances[$className])) {
            $this->apiInstances[$className] = new $className($this);
        }

        return $this->apiInstances[$className];
    }
}
