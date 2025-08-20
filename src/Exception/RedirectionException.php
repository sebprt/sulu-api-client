<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class RedirectionException extends ApiException
{
    public function __construct(string $message = 'Unexpected redirection', int $code = 3, ?\Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
