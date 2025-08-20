<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

/**
 * Exception thrown when an API request fails.
 */
class ApiException extends \Exception
{
    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
