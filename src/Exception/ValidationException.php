<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Exception;

class ValidationException extends ApiException
{
    /** @var array<string, mixed>|null */
    private readonly ?array $errors;

    /**
     * @param array<string, mixed>|null $errors
     */
    public function __construct(string $message = 'Validation Error', int $code = 422, ?\Throwable $previous = null, ?array $errors = null)
    {
        parent::__construct($message, $code, $previous, $errors);
        $this->errors = $errors;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
