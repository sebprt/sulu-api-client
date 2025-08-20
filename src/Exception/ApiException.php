<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

/**
 * Exception thrown when an API request fails.
 */
class ApiException extends \Exception
{
    /**
     * @param array<string, mixed>|null $responseData
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, private readonly ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the response data if available.
     *
     * @return array<string, mixed>|null
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
