<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

use Throwable;

class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Not Found', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
