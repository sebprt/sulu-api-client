<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class ForbiddenException extends ApiException
{
    public function __construct(string $message = 'Forbidden', int $code = 403, ?\Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
