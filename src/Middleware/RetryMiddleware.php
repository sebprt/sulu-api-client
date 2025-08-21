<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Middleware;

use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\TooManyRequestsException;

/**
 * Retry middleware that provides resilience by automatically retrying failed operations.
 * Uses exponential backoff strategy for retryable exceptions.
 */
final class RetryMiddleware
{
    public function __construct(
        private readonly int $maxRetries = 3,
        private readonly array $retryableStatusCodes = [429, 502, 503, 504],
        private readonly int $baseDelayMs = 1000,
    ) {
    }

    /**
     * Execute an operation with retry logic.
     *
     * @template T
     * @param callable(): T $operation
     * @return T
     * @throws \Exception
     */
    public function executeWithRetry(callable $operation): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                return $operation();
            } catch (TooManyRequestsException|ServerErrorException $e) {
                $lastException = $e;
                $attempt++;

                // Don't retry if we've reached max retries or if the status code is not retryable
                if ($attempt >= $this->maxRetries || !$this->isRetryableStatusCode($e->getCode())) {
                    throw $e;
                }

                $this->wait($attempt);
            }
        }

        // This should never be reached, but just in case
        throw $lastException ?? new \RuntimeException('Max retries exceeded without exception');
    }

    /**
     * Check if the given status code should trigger a retry.
     */
    private function isRetryableStatusCode(int $statusCode): bool
    {
        return in_array($statusCode, $this->retryableStatusCodes, true);
    }

    /**
     * Wait for the calculated delay based on attempt number using exponential backoff.
     */
    private function wait(int $attempt): void
    {
        $delay = $this->calculateDelay($attempt);
        usleep($delay * 1000); // usleep expects microseconds
    }

    /**
     * Calculate delay in milliseconds using exponential backoff with jitter.
     */
    private function calculateDelay(int $attempt): int
    {
        // Exponential backoff: baseDelay * (2 ^ (attempt - 1))
        $exponentialDelay = $this->baseDelayMs * (2 ** ($attempt - 1));
        
        // Add jitter to prevent thundering herd (±25% random variation)
        $jitter = (int) ($exponentialDelay * 0.25 * (mt_rand() / mt_getrandmax() * 2 - 1));
        
        return max(0, $exponentialDelay + $jitter);
    }

    /**
     * Get the maximum number of retries configured.
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * Get the base delay in milliseconds.
     */
    public function getBaseDelayMs(): int
    {
        return $this->baseDelayMs;
    }

    /**
     * Get the list of retryable status codes.
     */
    public function getRetryableStatusCodes(): array
    {
        return $this->retryableStatusCodes;
    }
}