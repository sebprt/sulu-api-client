<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Unauthorized', int $code = 401, ?\Throwable $previous = null, ?array $responseData = null)
    {
        parent::__construct($message, $code, $previous, $responseData);
    }
}
