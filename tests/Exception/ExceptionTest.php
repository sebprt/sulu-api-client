<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\Exception\ConflictException;
use Sulu\ApiClient\Exception\ForbiddenException;
use Sulu\ApiClient\Exception\InvalidJsonException;
use Sulu\ApiClient\Exception\MethodNotAllowedException;
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\PreconditionFailedException;
use Sulu\ApiClient\Exception\RedirectionException;
use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\TooManyRequestsException;
use Sulu\ApiClient\Exception\TransportException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Exception\UnexpectedResponseException;
use Sulu\ApiClient\Exception\UnsupportedMediaTypeException;
use Sulu\ApiClient\Exception\ValidationException;

class ExceptionTest extends TestCase
{
    public function testApiExceptionWithDefaults(): void
    {
        $exception = new ApiException();
        
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testApiExceptionWithCustomValues(): void
    {
        $message = 'Custom API error';
        $code = 500;
        
        $exception = new ApiException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testConflictException(): void
    {
        $exception = new ConflictException();
        
        $this->assertSame('Conflict', $exception->getMessage());
        $this->assertSame(409, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testConflictExceptionWithCustomValues(): void
    {
        $message = 'Resource conflict detected';
        $code = 999;
        
        $exception = new ConflictException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testForbiddenException(): void
    {
        $exception = new ForbiddenException();
        
        $this->assertSame('Forbidden', $exception->getMessage());
        $this->assertSame(403, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testForbiddenExceptionWithCustomValues(): void
    {
        $message = 'Access denied';
        $code = 999;
        
        $exception = new ForbiddenException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testInvalidJsonException(): void
    {
        $exception = new InvalidJsonException();
        
        $this->assertSame('Invalid JSON response body', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testInvalidJsonExceptionWithCustomValues(): void
    {
        $message = 'JSON parsing failed';
        $code = 400;
        
        $exception = new InvalidJsonException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testMethodNotAllowedException(): void
    {
        $exception = new MethodNotAllowedException();
        
        $this->assertSame('Method Not Allowed', $exception->getMessage());
        $this->assertSame(405, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testMethodNotAllowedExceptionWithCustomValues(): void
    {
        $message = 'HTTP method not supported';
        $code = 999;
        
        $exception = new MethodNotAllowedException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testNotFoundException(): void
    {
        $exception = new NotFoundException();
        
        $this->assertSame('Not Found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testNotFoundExceptionWithCustomValues(): void
    {
        $message = 'Resource not found';
        $code = 999;
        
        $exception = new NotFoundException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testPreconditionFailedException(): void
    {
        $exception = new PreconditionFailedException();
        
        $this->assertSame('Precondition Failed', $exception->getMessage());
        $this->assertSame(412, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testPreconditionFailedExceptionWithCustomValues(): void
    {
        $message = 'Precondition not met';
        $code = 999;
        
        $exception = new PreconditionFailedException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testRedirectionException(): void
    {
        $exception = new RedirectionException();
        
        $this->assertSame('Redirection', $exception->getMessage());
        $this->assertSame(302, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testRedirectionExceptionWithCustomValues(): void
    {
        $message = 'Resource moved';
        $code = 301;
        
        $exception = new RedirectionException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testServerErrorException(): void
    {
        $exception = new ServerErrorException();
        
        $this->assertSame('Server Error', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testServerErrorExceptionWithCustomValues(): void
    {
        $message = 'Internal server error occurred';
        $code = 503;
        
        $exception = new ServerErrorException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testTooManyRequestsException(): void
    {
        $exception = new TooManyRequestsException();
        
        $this->assertSame('Too Many Requests', $exception->getMessage());
        $this->assertSame(429, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testTooManyRequestsExceptionWithCustomValues(): void
    {
        $message = 'Rate limit exceeded';
        $code = 999;
        
        $exception = new TooManyRequestsException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testTransportException(): void
    {
        $exception = new TransportException();
        
        $this->assertSame('HTTP client error', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testTransportExceptionWithCustomValues(): void
    {
        $message = 'Network connection failed';
        $code = 408;
        
        $exception = new TransportException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testUnauthorizedException(): void
    {
        $exception = new UnauthorizedException();
        
        $this->assertSame('Unauthorized', $exception->getMessage());
        $this->assertSame(401, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testUnauthorizedExceptionWithCustomValues(): void
    {
        $message = 'Authentication required';
        $code = 999;
        
        $exception = new UnauthorizedException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testUnexpectedResponseException(): void
    {
        $exception = new UnexpectedResponseException();
        
        $this->assertSame('Unexpected Response', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testUnexpectedResponseExceptionWithCustomValues(): void
    {
        $message = 'Response format unexpected';
        $code = 500;
        
        $exception = new UnexpectedResponseException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testUnsupportedMediaTypeException(): void
    {
        $exception = new UnsupportedMediaTypeException();
        
        $this->assertSame('Unsupported Media Type', $exception->getMessage());
        $this->assertSame(415, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testUnsupportedMediaTypeExceptionWithCustomValues(): void
    {
        $message = 'Media type not supported';
        $code = 999;
        
        $exception = new UnsupportedMediaTypeException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testValidationException(): void
    {
        $exception = new ValidationException();
        
        $this->assertSame('Validation Error', $exception->getMessage());
        $this->assertSame(422, $exception->getCode());
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testValidationExceptionWithCustomValues(): void
    {
        $message = 'Invalid input data';
        $code = 999;
        
        $exception = new ValidationException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}