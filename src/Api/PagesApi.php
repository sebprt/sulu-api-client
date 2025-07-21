<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu pages.
 */
class PagesApi extends AbstractApi
{
    /**
     * Get a list of pages.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/pages', $parameters, $headers);
    }

    /**
     * Get a specific page by UUID.
     *
     * @param string $uuid The UUID of the page
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/pages/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new page.
     *
     * @param array $data The page data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/pages', $parameters, $data);
    }

    /**
     * Update an existing page.
     *
     * @param string $uuid The UUID of the page
     * @param array $data The page data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/pages/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a page.
     *
     * @param string $uuid The UUID of the page
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function deletePage($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/pages/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Get the children of a page.
     *
     * @param string $uuid The UUID of the parent page
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getChildren($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/pages/{uuid}/children', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Copy a page.
     *
     * @param string $uuid The UUID of the page to copy
     * @param array $data The copy data (e.g., destination)
     * @param array $parameters Query parameters
     * @return array
     */
    public function copy($uuid, array $data, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/pages/{uuid}/copy', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Move a page.
     *
     * @param string $uuid The UUID of the page to move
     * @param array $data The move data (e.g., destination)
     * @param array $parameters Query parameters
     * @return array
     */
    public function move($uuid, array $data, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/pages/{uuid}/move', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Publish a page.
     *
     * @param string $uuid The UUID of the page to publish
     * @param array $parameters Query parameters
     * @return array
     */
    public function publish($uuid, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/pages/{uuid}/publish', ['uuid' => $uuid]), $parameters);
    }

    /**
     * Unpublish a page.
     *
     * @param string $uuid The UUID of the page to unpublish
     * @param array $parameters Query parameters
     * @return array
     */
    public function unpublish($uuid, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/pages/{uuid}/unpublish', ['uuid' => $uuid]), $parameters);
    }
}
