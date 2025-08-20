<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class InvalidJsonException extends ApiException
{
    public function __construct(string $message = 'Invalid JSON response body', int $code = 0, ?\Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
