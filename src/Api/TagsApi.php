<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu tags.
 */
class TagsApi extends AbstractApi
{
    /**
     * Get a list of tags.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/tags', $parameters, $headers);
    }

    /**
     * Get a specific tag by UUID.
     *
     * @param string $uuid The UUID of the tag
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/tags/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new tag.
     *
     * @param array $data The tag data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/tags', $parameters, $data);
    }

    /**
     * Update an existing tag.
     *
     * @param string $uuid The UUID of the tag
     * @param array $data The tag data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/tags/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a tag.
     *
     * @param string $uuid The UUID of the tag
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/tags/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Merge tags.
     *
     * @param string $sourceUuid The UUID of the source tag
     * @param string $destinationUuid The UUID of the destination tag
     * @param array $parameters Query parameters
     * @return array
     */
    public function merge($sourceUuid, $destinationUuid, array $parameters = [])
    {
        $data = [
            'destinationTag' => $destinationUuid
        ];

        return $this->post($this->buildPath('/admin/api/tags/{uuid}/merge', ['uuid' => $sourceUuid]), $parameters, $data);
    }
}
