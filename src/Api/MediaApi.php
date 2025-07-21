<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu media.
 */
class MediaApi extends AbstractApi
{
    /**
     * Get a list of media items.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/media', $parameters, $headers);
    }

    /**
     * Get a specific media item by UUID.
     *
     * @param string $uuid The UUID of the media item
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/media/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new media item.
     *
     * @param array $data The media data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/media', $parameters, $data);
    }

    /**
     * Update an existing media item.
     *
     * @param string $uuid The UUID of the media item
     * @param array $data The media data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/media/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a media item.
     *
     * @param string $uuid The UUID of the media item
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function deleteMedia($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/media/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Upload a file to create a new media item.
     *
     * @param string $collectionId The collection ID to upload to
     * @param string $filePath The path to the file to upload
     * @param string $title The title of the media item
     * @param array $parameters Additional query parameters
     * @return array
     */
    public function upload($collectionId, $filePath, $title, array $parameters = [])
    {
        $fileContent = file_get_contents($filePath);
        $fileName = basename($filePath);

        $multipart = [
            [
                'name' => 'fileVersion',
                'contents' => $fileContent,
                'filename' => $fileName
            ],
            [
                'name' => 'collection',
                'contents' => $collectionId
            ],
            [
                'name' => 'title',
                'contents' => $title
            ]
        ];

        $options = [
            'multipart' => $multipart
        ];

        $response = $this->client->getHttpClient()->request('POST', '/admin/api/media', $options);
        $content = $response->getBody()->getContents();

        return json_decode($content, true);
    }

    /**
     * Get media formats.
     *
     * @param string $uuid The UUID of the media item
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getFormats($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/media/{uuid}/formats', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Get media preview URL.
     *
     * @param string $uuid The UUID of the media item
     * @param string $format The format name
     * @return string
     */
    public function getPreviewUrl($uuid, $format = 'sulu-small')
    {
        return $this->client->getBaseUrl() . '/media/' . $uuid . '/' . $format;
    }
}
