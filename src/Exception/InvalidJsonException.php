<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class InvalidJsonException extends ApiException
{
    public function __construct(string $message = 'Invalid JSON response body', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
