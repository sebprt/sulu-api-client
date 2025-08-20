<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class UnexpectedResponseException extends ApiException
{
    public function __construct(string $message = 'API error', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
