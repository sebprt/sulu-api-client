<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class MethodNotAllowedException extends ApiException
{
    public function __construct(string $message = 'Method Not Allowed', int $code = 405, ?Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
