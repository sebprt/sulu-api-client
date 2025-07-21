<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu categories.
 */
class CategoriesApi extends AbstractApi
{
    /**
     * Get a list of categories.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/categories', $parameters, $headers);
    }

    /**
     * Get a specific category by UUID.
     *
     * @param string $uuid The UUID of the category
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/categories/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new category.
     *
     * @param array $data The category data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/categories', $parameters, $data);
    }

    /**
     * Update an existing category.
     *
     * @param string $uuid The UUID of the category
     * @param array $data The category data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/categories/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a category.
     *
     * @param string $uuid The UUID of the category
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function deleteCategory($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/categories/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Get the children of a category.
     *
     * @param string $uuid The UUID of the parent category
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getChildren($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/categories/{uuid}/children', ['uuid' => $uuid]), $parameters, $headers);
    }
}
