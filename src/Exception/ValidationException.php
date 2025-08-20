<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class ValidationException extends ApiException
{
    public function __construct(string $message = 'Validation Error', int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
