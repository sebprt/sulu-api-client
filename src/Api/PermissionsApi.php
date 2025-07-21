<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu permissions.
 */
class PermissionsApi extends AbstractApi
{
    /**
     * Get a list of permissions.
     *
     * @param array $parameters Query parameters
     * @return array
     */
    public function getList(array $parameters = [])
    {
        return $this->get('/admin/api/permissions', $parameters);
    }

    /**
     * Get permissions for a specific context.
     *
     * @param string $context The context (e.g., 'sulu.contact.people')
     * @param array $parameters Query parameters
     * @return array
     */
    public function getContextPermissions($context, array $parameters = [])
    {
        $parameters['context'] = $context;

        return $this->get('/admin/api/permissions', $parameters);
    }

    /**
     * Get permissions for a specific object.
     *
     * @param string $type The object type (e.g., 'pages', 'media')
     * @param string $id The object ID
     * @param array $parameters Query parameters
     * @return array
     */
    public function getObjectPermissions($type, $id, array $parameters = [])
    {
        return $this->get($this->buildPath('/admin/api/permissions/{type}/{id}', [
            'type' => $type,
            'id' => $id
        ]), $parameters);
    }

    /**
     * Set permissions for a specific object.
     *
     * @param string $type The object type (e.g., 'pages', 'media')
     * @param string $id The object ID
     * @param array $data The permissions data
     * @param array $parameters Query parameters
     * @return array
     */
    public function setObjectPermissions($type, $id, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/permissions/{type}/{id}', [
            'type' => $type,
            'id' => $id
        ]), $parameters, $data);
    }

    /**
     * Get security contexts.
     *
     * @param array $parameters Query parameters
     * @return array
     */
    public function getSecurityContexts(array $parameters = [])
    {
        return $this->get('/admin/api/security-contexts', $parameters);
    }

    /**
     * Get security context permissions.
     *
     * @param string $context The security context
     * @param array $parameters Query parameters
     * @return array
     */
    public function getSecurityContextPermissions($context, array $parameters = [])
    {
        return $this->get($this->buildPath('/admin/api/security-contexts/{context}', [
            'context' => $context
        ]), $parameters);
    }
}
