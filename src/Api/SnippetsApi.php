<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu snippets.
 */
class SnippetsApi extends AbstractApi
{
    /**
     * Get a list of snippets.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/snippets', $parameters, $headers);
    }

    /**
     * Get a specific snippet by UUID.
     *
     * @param string $uuid The UUID of the snippet
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/snippets/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new snippet.
     *
     * @param array $data The snippet data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/snippets', $parameters, $data);
    }

    /**
     * Update an existing snippet.
     *
     * @param string $uuid The UUID of the snippet
     * @param array $data The snippet data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/snippets/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a snippet.
     *
     * @param string $uuid The UUID of the snippet
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function deleteSnippet($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/snippets/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Publish a snippet.
     *
     * @param string $uuid The UUID of the snippet to publish
     * @param array $parameters Query parameters
     * @return array
     */
    public function publish($uuid, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/snippets/{uuid}/publish', ['uuid' => $uuid]), $parameters);
    }

    /**
     * Unpublish a snippet.
     *
     * @param string $uuid The UUID of the snippet to unpublish
     * @param array $parameters Query parameters
     * @return array
     */
    public function unpublish($uuid, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/snippets/{uuid}/unpublish', ['uuid' => $uuid]), $parameters);
    }

    /**
     * Get snippet defaults.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getDefaults(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/snippet-defaults', $parameters, $headers);
    }

    /**
     * Get snippet areas.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getAreas(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/snippet-areas', $parameters, $headers);
    }
}
