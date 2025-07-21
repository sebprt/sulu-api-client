<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu users.
 */
class UserApi extends AbstractApi
{
    /**
     * Get a list of users.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/users', $parameters, $headers);
    }

    /**
     * Get a specific user by ID.
     *
     * @param string $id The ID of the user
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getById($id, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/users/{id}', ['id' => $id]), $parameters, $headers);
    }

    /**
     * Create a new user.
     *
     * @param array $data The user data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/users', $parameters, $data);
    }

    /**
     * Update an existing user.
     *
     * @param string $id The ID of the user
     * @param array $data The user data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($id, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/users/{id}', ['id' => $id]), $parameters, $data);
    }

    /**
     * Delete a user.
     *
     * @param string $id The ID of the user
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($id, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/users/{id}', ['id' => $id]), $parameters, $body, $headers);
    }

    /**
     * Enable a user.
     *
     * @param string $id The ID of the user
     * @param array $parameters Query parameters
     * @return array
     */
    public function enable($id, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/users/{id}/enable', ['id' => $id]), $parameters);
    }

    /**
     * Lock a user.
     *
     * @param string $id The ID of the user
     * @param array $parameters Query parameters
     * @return array
     */
    public function lock($id, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/users/{id}/lock', ['id' => $id]), $parameters);
    }

    /**
     * Unlock a user.
     *
     * @param string $id The ID of the user
     * @param array $parameters Query parameters
     * @return array
     */
    public function unlock($id, array $parameters = [])
    {
        return $this->post($this->buildPath('/admin/api/users/{id}/unlock', ['id' => $id]), $parameters);
    }

    /**
     * Get the current user.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function me(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/users/me', $parameters, $headers);
    }

    /**
     * Change the password of a user.
     *
     * @param string $id The ID of the user
     * @param string $password The new password
     * @param array $parameters Query parameters
     * @return array
     */
    public function changePassword($id, $password, array $parameters = [])
    {
        $data = [
            'password' => $password
        ];

        return $this->post($this->buildPath('/admin/api/users/{id}/password', ['id' => $id]), $parameters, $data);
    }
}
