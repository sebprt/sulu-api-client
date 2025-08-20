<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class TooManyRequestsException extends ApiException
{
    public function __construct(string $message = 'Too Many Requests', int $code = 429)
    {
        parent::__construct($message, $code);
    }
}
