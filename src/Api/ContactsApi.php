<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu contacts.
 */
class ContactsApi extends AbstractApi
{
    /**
     * Get a list of contacts.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/contacts', $parameters, $headers);
    }

    /**
     * Get a specific contact by UUID.
     *
     * @param string $uuid The UUID of the contact
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/contacts/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new contact.
     *
     * @param array $data The contact data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/contacts', $parameters, $data);
    }

    /**
     * Update an existing contact.
     *
     * @param string $uuid The UUID of the contact
     * @param array $data The contact data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/contacts/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete a contact.
     *
     * @param string $uuid The UUID of the contact
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/contacts/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Get contact medias.
     *
     * @param string $uuid The UUID of the contact
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getMedias($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/contacts/{uuid}/medias', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Add a media to a contact.
     *
     * @param string $uuid The UUID of the contact
     * @param string $mediaId The UUID of the media
     * @param array $parameters Query parameters
     * @return array
     */
    public function addMedia($uuid, $mediaId, array $parameters = [])
    {
        $data = [
            'mediaId' => $mediaId
        ];

        return $this->post($this->buildPath('/admin/api/contacts/{uuid}/medias', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Remove a media from a contact.
     *
     * @param string $uuid The UUID of the contact
     * @param string $mediaId The UUID of the media
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function removeMedia($uuid, $mediaId, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/contacts/{uuid}/medias/{mediaId}', [
            'uuid' => $uuid,
            'mediaId' => $mediaId
        ]), $parameters, $body, $headers);
    }

    /**
     * Get contact accounts.
     *
     * @param string $uuid The UUID of the contact
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getAccounts($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/contacts/{uuid}/accounts', ['uuid' => $uuid]), $parameters, $headers);
    }
}
