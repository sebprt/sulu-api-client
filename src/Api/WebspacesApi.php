<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu webspaces.
 */
class WebspacesApi extends AbstractApi
{
    /**
     * Get a list of webspaces.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/webspaces', $parameters, $headers);
    }

    /**
     * Get a specific webspace by key.
     *
     * @param string $key The key of the webspace
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByKey($key, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/webspaces/{key}', ['key' => $key]), $parameters, $headers);
    }

    /**
     * Get webspace localizations.
     *
     * @param string $key The key of the webspace
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getLocalizations($key, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/webspaces/{key}/localizations', ['key' => $key]), $parameters, $headers);
    }

    /**
     * Get webspace custom URLs.
     *
     * @param string $key The key of the webspace
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getCustomUrls($key, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/webspaces/{key}/custom-urls', ['key' => $key]), $parameters, $headers);
    }

    /**
     * Get webspace segments.
     *
     * @param string $key The key of the webspace
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getSegments($key, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/webspaces/{key}/segments', ['key' => $key]), $parameters, $headers);
    }

    /**
     * Get webspace domains.
     *
     * @param string $key The key of the webspace
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getDomains($key, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/webspaces/{key}/domains', ['key' => $key]), $parameters, $headers);
    }
}
