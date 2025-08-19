<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class ConflictException extends ApiException
{
    public function __construct(string $message = 'Conflict', int $code = 409, ?Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
