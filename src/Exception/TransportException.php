<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class TransportException extends ApiException
{
    public function __construct(string $message = 'HTTP client error', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
