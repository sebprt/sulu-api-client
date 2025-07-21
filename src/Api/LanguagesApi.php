<?php

namespace Sulu\ApiClient\Api;

/**
 * API client for Sulu languages.
 */
class LanguagesApi extends AbstractApi
{
    /**
     * Get a list of languages.
     *
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getList(array $parameters = [], array $headers = [])
    {
        return $this->get('/admin/api/languages', $parameters, $headers);
    }

    /**
     * Get a specific language by code.
     *
     * @param string $code The code of the language
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getByCode($code, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/languages/{code}', ['code' => $code]), $parameters, $headers);
    }

    /**
     * Create a new language.
     *
     * @param array $data The language data
     * @param array $parameters Query parameters
     * @return array
     */
    public function create(array $data, array $parameters = [])
    {
        return $this->post('/admin/api/languages', $parameters, $data);
    }

    /**
     * Update an existing language.
     *
     * @param string $code The code of the language
     * @param array $data The language data
     * @param array $parameters Query parameters
     * @return array
     */
    public function update($code, array $data, array $parameters = [])
    {
        return $this->put($this->buildPath('/admin/api/languages/{code}', ['code' => $code]), $parameters, $data);
    }

    /**
     * Delete a language.
     *
     * @param string $code The code of the language
     * @param array $parameters Query parameters
     * @param array $body Request body
     * @param array $headers HTTP headers
     * @return array
     */
    public function delete($code, array $parameters = [], array $body = [], array $headers = [])
    {
        return $this->delete($this->buildPath('/admin/api/languages/{code}', ['code' => $code]), $parameters, $body, $headers);
    }

    /**
     * Get language localizations.
     *
     * @param string $code The code of the language
     * @param array $parameters Query parameters
     * @param array $headers HTTP headers
     * @return array
     */
    public function getLocalizations($code, array $parameters = [], array $headers = [])
    {
        return $this->get($this->buildPath('/admin/api/languages/{code}/localizations', ['code' => $code]), $parameters, $headers);
    }
}
