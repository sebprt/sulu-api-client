<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class MethodNotAllowedException extends ApiException
{
    public function __construct(string $message = 'Method Not Allowed', int $code = 405)
    {
        parent::__construct($message, $code);
    }
}
