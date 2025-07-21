<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu accounts.
 */
class AccountsApi extends AbstractApi
{
    /**
     * Get a list of accounts.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/accounts', $parameters, $headers);
    }

    /**
     * Get a specific account by UUID.
     *
     * @param string $uuid The UUID of the account
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByUuid($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/accounts/{uuid}', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Create a new account.
     *
     * @param array $data The account data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/accounts', $parameters, $data);
    }

    /**
     * Update an existing account.
     *
     * @param string $uuid The UUID of the account
     * @param array $data The account data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($uuid, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/accounts/{uuid}', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Delete an account.
     *
     * @param string $uuid The UUID of the account
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($uuid, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/accounts/{uuid}', ['uuid' => $uuid]), $parameters, $body, $headers);
    }

    /**
     * Get account contacts.
     *
     * @param string $uuid The UUID of the account
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getContacts($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/accounts/{uuid}/contacts', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Add a contact to an account.
     *
     * @param string $uuid The UUID of the account
     * @param string $contactId The UUID of the contact
     * @param array $data Additional data for the relation
     * @param array $parameters Query parameters
     * @return array
     */
    public function addContact($uuid, $contactId, array $data = [], array $parameters = [])
    {
        $data['contactId'] = $contactId;

        return $this->post($this->buildPath('/admin/api/accounts/{uuid}/contacts', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Remove a contact from an account.
     *
     * @param string $uuid The UUID of the account
     * @param string $contactId The UUID of the contact
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function removeContact($uuid, $contactId, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/accounts/{uuid}/contacts/{contactId}', [
            'uuid' => $uuid,
            'contactId' => $contactId
        ]), $parameters, $body, $headers);
    }

    /**
     * Get account medias.
     *
     * @param string $uuid The UUID of the account
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getMedias($uuid, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/accounts/{uuid}/medias', ['uuid' => $uuid]), $parameters, $headers);
    }

    /**
     * Add a media to an account.
     *
     * @param string $uuid The UUID of the account
     * @param string $mediaId The UUID of the media
     * @param array $parameters Query parameters
     * @return array
     */
    public function addMedia($uuid, $mediaId, array $parameters = [])
    {
        $data = [
            'mediaId' => $mediaId
        ];

        return $this->post($this->buildPath('/admin/api/accounts/{uuid}/medias', ['uuid' => $uuid]), $parameters, $data);
    }

    /**
     * Remove a media from an account.
     *
     * @param string $uuid The UUID of the account
     * @param string $mediaId The UUID of the media
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function removeMedia($uuid, $mediaId, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/accounts/{uuid}/medias/{mediaId}', [
            'uuid' => $uuid,
            'mediaId' => $mediaId
        ]), $parameters, $body, $headers);
    }
}
