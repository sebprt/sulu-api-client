<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\TooManyRequestsException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Middleware\RetryMiddleware;

class RetryMiddlewareTest extends TestCase
{
    private RetryMiddleware $retryMiddleware;

    protected function setUp(): void
    {
        $this->retryMiddleware = new RetryMiddleware(
            maxRetries: 3,
            retryableStatusCodes: [429, 502, 503, 504],
            baseDelayMs: 100 // Use smaller delay for tests
        );
    }

    public function testExecuteWithRetrySucceedsOnFirstAttempt(): void
    {
        $expectedResult = 'success';
        $callCount = 0;

        $operation = function () use ($expectedResult, &$callCount) {
            $callCount++;
            return $expectedResult;
        };

        $result = $this->retryMiddleware->executeWithRetry($operation);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals(1, $callCount);
    }

    public function testExecuteWithRetryRetriesOnTooManyRequestsException(): void
    {
        $expectedResult = 'success';
        $callCount = 0;

        $operation = function () use ($expectedResult, &$callCount) {
            $callCount++;
            if ($callCount < 3) {
                throw new TooManyRequestsException('Too many requests', 429);
            }
            return $expectedResult;
        };

        $startTime = microtime(true);
        $result = $this->retryMiddleware->executeWithRetry($operation);
        $endTime = microtime(true);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals(3, $callCount);
        
        // Should have some delay (at least 100ms for first retry + 200ms for second retry)
        $this->assertGreaterThan(0.25, $endTime - $startTime);
    }

    public function testExecuteWithRetryRetriesOnServerErrorException(): void
    {
        $expectedResult = 'success';
        $callCount = 0;

        $operation = function () use ($expectedResult, &$callCount) {
            $callCount++;
            if ($callCount < 2) {
                throw new ServerErrorException('Server error', 503);
            }
            return $expectedResult;
        };

        $result = $this->retryMiddleware->executeWithRetry($operation);

        $this->assertEquals($expectedResult, $result);
        $this->assertEquals(2, $callCount);
    }

    public function testExecuteWithRetryThrowsAfterMaxRetries(): void
    {
        $callCount = 0;
        $exception = new TooManyRequestsException('Too many requests', 429);

        $operation = function () use ($exception, &$callCount) {
            $callCount++;
            throw $exception;
        };

        $this->expectException(TooManyRequestsException::class);
        $this->expectExceptionMessage('Too many requests');

        $this->retryMiddleware->executeWithRetry($operation);

        $this->assertEquals(3, $callCount);
    }

    public function testExecuteWithRetryDoesNotRetryNonRetryableExceptions(): void
    {
        $callCount = 0;
        $exception = new UnauthorizedException('Unauthorized', 401);

        $operation = function () use ($exception, &$callCount) {
            $callCount++;
            throw $exception;
        };

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->retryMiddleware->executeWithRetry($operation);

        $this->assertEquals(1, $callCount);
    }

    public function testExecuteWithRetryDoesNotRetryNonRetryableStatusCode(): void
    {
        $callCount = 0;
        $exception = new ServerErrorException('Internal Server Error', 500);

        $operation = function () use ($exception, &$callCount) {
            $callCount++;
            throw $exception;
        };

        $this->expectException(ServerErrorException::class);
        $this->expectExceptionMessage('Internal Server Error');

        $this->retryMiddleware->executeWithRetry($operation);

        $this->assertEquals(1, $callCount);
    }

    /**
     * @dataProvider retryableStatusCodeProvider
     */
    public function testRetryableStatusCodesAreHandledCorrectly(int $statusCode, bool $shouldRetry): void
    {
        $callCount = 0;
        $exception = new ServerErrorException('Error', $statusCode);

        $operation = function () use ($exception, &$callCount) {
            $callCount++;
            throw $exception;
        };

        $this->expectException(ServerErrorException::class);

        try {
            $this->retryMiddleware->executeWithRetry($operation);
        } catch (ServerErrorException $e) {
            if ($shouldRetry) {
                $this->assertEquals(3, $callCount, "Should retry for status code {$statusCode}");
            } else {
                $this->assertEquals(1, $callCount, "Should not retry for status code {$statusCode}");
            }
            throw $e;
        }
    }

    public function retryableStatusCodeProvider(): array
    {
        return [
            'Too Many Requests' => [429, true],
            'Bad Gateway' => [502, true],
            'Service Unavailable' => [503, true],
            'Gateway Timeout' => [504, true],
            'Internal Server Error' => [500, false],
            'Not Found' => [404, false],
            'Unauthorized' => [401, false],
        ];
    }

    public function testExponentialBackoffCalculation(): void
    {
        $retryMiddleware = new RetryMiddleware(maxRetries: 3, baseDelayMs: 1000);
        
        // Test delay calculation indirectly by measuring execution time
        $callCount = 0;
        $operation = function () use (&$callCount) {
            $callCount++;
            if ($callCount <= 2) {
                throw new TooManyRequestsException('Rate limited', 429);
            }
            return 'success';
        };

        $startTime = microtime(true);
        $result = $retryMiddleware->executeWithRetry($operation);
        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        // Should take at least 1000ms (first retry) + 2000ms (second retry) = 3000ms
        // But with jitter, it could be less, so we check for at least 2 seconds
        $this->assertGreaterThan(2.0, $executionTime);
        $this->assertEquals('success', $result);
        $this->assertEquals(3, $callCount);
    }

    public function testGettersReturnCorrectValues(): void
    {
        $retryMiddleware = new RetryMiddleware(
            maxRetries: 5,
            retryableStatusCodes: [429, 502, 503],
            baseDelayMs: 2000
        );

        $this->assertEquals(5, $retryMiddleware->getMaxRetries());
        $this->assertEquals(2000, $retryMiddleware->getBaseDelayMs());
        $this->assertEquals([429, 502, 503], $retryMiddleware->getRetryableStatusCodes());
    }

    public function testDefaultConfiguration(): void
    {
        $defaultRetryMiddleware = new RetryMiddleware();

        $this->assertEquals(3, $defaultRetryMiddleware->getMaxRetries());
        $this->assertEquals(1000, $defaultRetryMiddleware->getBaseDelayMs());
        $this->assertEquals([429, 502, 503, 504], $defaultRetryMiddleware->getRetryableStatusCodes());
    }
}