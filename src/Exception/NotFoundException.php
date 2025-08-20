<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Not Found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
