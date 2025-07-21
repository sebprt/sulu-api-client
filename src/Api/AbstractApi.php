<?php

namespace Sulu\ApiClient\Api;

use GuzzleHttp\Exception\GuzzleException;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\SuluClient;

/**
 * Abstract base class for all API endpoints.
 */
abstract class AbstractApi
{
    /**
     * @var SuluClient
     */
    protected $client;

    /**
     * @param SuluClient $client
     */
    public function __construct(SuluClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send a GET request.
     *
     * @param string $path
     * @param array $parameters
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function get($path, array $parameters = [], array $headers = [])
    {
        return $this->request('GET', $path, $parameters, [], $headers);
    }

    /**
     * Send a POST request.
     *
     * @param string $path
     * @param array $parameters
     * @param array $body
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function post($path, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->request('POST', $path, $parameters, $body, $headers);
    }

    /**
     * Send a PUT request.
     *
     * @param string $path
     * @param array $parameters
     * @param array $body
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function put($path, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->request('PUT', $path, $parameters, $body, $headers);
    }

    /**
     * Send a DELETE request.
     *
     * @param string $path
     * @param array $parameters
     * @param array $body
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function delete($path, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->request('DELETE', $path, $parameters, $body, $headers);
    }

    /**
     * Send a PATCH request.
     *
     * @param string $path
     * @param array $parameters
     * @param array $body
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function patch($path, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->request('PATCH', $path, $parameters, $body, $headers);
    }

    /**
     * Prepares the path by prepending '/admin' to it.
     *
     * @param string $path
     * @return string
     */
    protected function preparePath($path)
    {
        if (strpos($path, '/admin') === 0) {
            return $path;
        }
        return '/admin' . $path;
    }

    /**
     * Send a request to the API.
     *
     * @param string $method
     * @param string $path
     * @param array $parameters
     * @param array $body
     * @param array $headers
     * @return array
     * @throws ApiException
     */
    protected function request($method, $path, array $parameters = [], array $body = [], array $headers = [])
    {
        $options = [];

        if (!empty($parameters)) {
            $options['query'] = $parameters;
        }

        if (!empty($body)) {
            $options['json'] = $body;
        }

        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        try {
            $response = $this->client->getHttpClient()->request($method, $this->preparePath($path), $options);
            $content = $response->getBody()->getContents();

            return json_decode($content, true);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Build a path with URI parameters.
     *
     * @param string $path
     * @param array $parameters
     * @return string
     */
    protected function buildPath($path, array $parameters = [])
    {
        foreach ($parameters as $name => $value) {
            $path = str_replace(sprintf('{%s}', $name), $value, $path);
        }

        return $path;
    }
}
