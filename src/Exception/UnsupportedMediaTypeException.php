<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class UnsupportedMediaTypeException extends ApiException
{
    public function __construct(string $message = 'Unsupported Media Type', int $code = 415)
    {
        parent::__construct($message, $code);
    }
}
