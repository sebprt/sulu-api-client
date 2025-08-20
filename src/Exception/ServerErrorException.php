<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class ServerErrorException extends ApiException
{
    public function __construct(string $message = 'Server error', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
