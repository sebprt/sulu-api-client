<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class ConflictException extends ApiException
{
    public function __construct(string $message = 'Conflict', int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
