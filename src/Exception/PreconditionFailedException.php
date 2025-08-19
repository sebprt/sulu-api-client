<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class PreconditionFailedException extends ApiException
{
    public function __construct(string $message = 'Precondition Failed', int $code = 412, ?Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
