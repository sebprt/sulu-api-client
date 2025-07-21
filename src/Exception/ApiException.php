<?php

namespace Sulu\ApiClient\Exception;

use Exception;

/**
 * Exception thrown when an API request fails.
 */
class ApiException extends Exception
{
    /**
     * @var array|null
     */
    private $responseData;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @param array|null $responseData
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, $responseData = null)
    {
        parent::__construct($message, $code, $previous);
        $this->responseData = $responseData;
    }

    /**
     * Get the response data if available.
     *
     * @return array|null
     */
    public function getResponseData()
    {
        return $this->responseData;
    }
}
