<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu roles.
 */
class RolesApi extends AbstractApi
{
    /**
     * Get a list of roles.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/roles', $parameters, $headers);
    }

    /**
     * Get a specific role by ID.
     *
     * @param string $id The ID of the role
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getById($id, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/roles/{id}', ['id' => $id]), $parameters, $headers);
    }

    /**
     * Create a new role.
     *
     * @param array $data The role data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/roles', $parameters, $data);
    }

    /**
     * Update an existing role.
     *
     * @param string $id The ID of the role
     * @param array $data The role data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($id, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/roles/{id}', ['id' => $id]), $parameters, $data);
    }

    /**
     * Delete a role.
     *
     * @param string $id The ID of the role
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($id, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/roles/{id}', ['id' => $id]), $parameters, $body, $headers);
    }

    /**
     * Get role permissions.
     *
     * @param string $id The ID of the role
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getPermissions($id, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/api/roles/{id}/permissions', ['id' => $id]), $parameters, $headers);
    }

    /**
     * Set role permissions.
     *
     * @param string $id The ID of the role
     * @param array $permissions The permissions data
     * @param array $parameters Query parameters
     * @return array
     */
    public function setPermissions($id, array $permissions, array $parameters = [])
    {
        return $this->put($this->buildPath('/api/roles/{id}/permissions', ['id' => $id]), $parameters, $permissions);
    }

    /**
     * Get role users.
     *
     * @param string $id The ID of the role
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getUsers($id, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/api/roles/{id}/users', ['id' => $id]), $parameters, $headers);
    }

    /**
     * Add a user to a role.
     *
     * @param string $id The ID of the role
     * @param string $userId The ID of the user
     * @param array $parameters Query parameters
     * @return array
     */
    public function addUser($id, $userId, array $parameters = [])
    {
        $data = [
            'userId' => $userId
        ];

        return $this->post($this->buildPath('/api/roles/{id}/users', ['id' => $id]), $parameters, $data);
    }

    /**
     * Remove a user from a role.
     *
     * @param string $id The ID of the role
     * @param string $userId The ID of the user
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function removeUser($id, $userId, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/api/roles/{id}/users/{userId}', [
            'id' => $id,
            'userId' => $userId
        ]), $parameters, $body, $headers);
    }
}
