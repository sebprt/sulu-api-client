<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class InvalidJsonException extends ApiException
{
    public function __construct(string $message = 'Invalid JSON response body', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
