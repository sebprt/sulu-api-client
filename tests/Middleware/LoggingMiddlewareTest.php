<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Middleware\LoggingMiddleware;
use Sulu\ApiClient\Tests\Fixtures\SimpleRequest;

final class LoggingMiddlewareTest extends TestCase
{
    public function testAuthenticateLogsRequestAndDelegatesToInner(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $innerAuth = $this->createMock(RequestAuthenticatorInterface::class);
        
        $request = new SimpleRequest('POST', 'https://api.example.com/users');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withHeader('User-Agent', 'SuluApiClient/1.0');
        
        $authenticatedRequest = $request->withHeader('Authorization', 'Bearer token123');
        
        // Expect logging with correct data
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'API Request',
                [
                    'method' => 'POST',
                    'uri' => 'https://api.example.com/users',
                    'headers' => [
                        'content-type' => ['application/json'],
                        'user-agent' => ['SuluApiClient/1.0'],
                        // Authorization should be sanitized out
                    ],
                ]
            );
        
        // Expect delegation to inner authenticator
        $innerAuth->expects(self::once())
            ->method('authenticate')
            ->with($request)
            ->willReturn($authenticatedRequest);
        
        $middleware = new LoggingMiddleware($innerAuth, $logger);
        $result = $middleware->authenticate($request);
        
        self::assertSame($authenticatedRequest, $result);
    }

    public function testSanitizeHeadersRemovesSensitiveHeaders(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $innerAuth = $this->createMock(RequestAuthenticatorInterface::class);
        
        $request = new SimpleRequest('GET', 'https://api.example.com/data');
        $request = $request->withHeader('Authorization', 'Bearer secret-token');
        $request = $request->withHeader('Cookie', 'session=abc123');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withHeader('Accept', 'application/json');
        
        $authenticatedRequest = $request->withHeader('X-Custom', 'value');
        
        // Expect logging without sensitive headers
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'API Request',
                [
                    'method' => 'GET',
                    'uri' => 'https://api.example.com/data',
                    'headers' => [
                        'content-type' => ['application/json'],
                        'accept' => ['application/json'],
                        // Authorization and Cookie should be removed
                    ],
                ]
            );
        
        $innerAuth->expects(self::once())
            ->method('authenticate')
            ->with($request)
            ->willReturn($authenticatedRequest);
        
        $middleware = new LoggingMiddleware($innerAuth, $logger);
        $result = $middleware->authenticate($request);
        
        self::assertSame($authenticatedRequest, $result);
    }

    public function testHandlesRequestWithoutSensitiveHeaders(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $innerAuth = $this->createMock(RequestAuthenticatorInterface::class);
        
        $request = new SimpleRequest('DELETE', 'https://api.example.com/items/1');
        $request = $request->withHeader('Accept', 'application/json');
        
        $authenticatedRequest = $request->withHeader('Authorization', 'Bearer added-token');
        
        // Expect logging with all headers (no sensitive ones to remove)
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'API Request',
                [
                    'method' => 'DELETE',
                    'uri' => 'https://api.example.com/items/1',
                    'headers' => [
                        'accept' => ['application/json'],
                    ],
                ]
            );
        
        $innerAuth->expects(self::once())
            ->method('authenticate')
            ->with($request)
            ->willReturn($authenticatedRequest);
        
        $middleware = new LoggingMiddleware($innerAuth, $logger);
        $result = $middleware->authenticate($request);
        
        self::assertSame($authenticatedRequest, $result);
    }

    public function testMaintainsRequestImmutability(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $innerAuth = $this->createMock(RequestAuthenticatorInterface::class);
        
        $originalRequest = new SimpleRequest('PUT', 'https://api.example.com/resource');
        $authenticatedRequest = $originalRequest->withHeader('Authorization', 'Bearer token');
        
        $logger->expects(self::once())->method('debug');
        $innerAuth->expects(self::once())
            ->method('authenticate')
            ->with($originalRequest)
            ->willReturn($authenticatedRequest);
        
        $middleware = new LoggingMiddleware($innerAuth, $logger);
        $result = $middleware->authenticate($originalRequest);
        
        // Original request should be unchanged
        self::assertSame('', $originalRequest->getHeaderLine('Authorization'));
        // Result should be the authenticated request
        self::assertSame($authenticatedRequest, $result);
        self::assertSame('Bearer token', $result->getHeaderLine('Authorization'));
    }
}