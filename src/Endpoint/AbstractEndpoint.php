<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Exception\ForbiddenException;
use Sulu\ApiClient\Exception\MethodNotAllowedException;
use Sulu\ApiClient\Exception\ConflictException;
use Sulu\ApiClient\Exception\PreconditionFailedException;
use Sulu\ApiClient\Exception\UnsupportedMediaTypeException;
use Sulu\ApiClient\Exception\TooManyRequestsException;
use Sulu\ApiClient\Exception\RedirectionException;
use Sulu\ApiClient\Exception\ServerErrorException;
use Sulu\ApiClient\Exception\InvalidJsonException;
use Sulu\ApiClient\Exception\TransportException;
use Sulu\ApiClient\Exception\UnexpectedResponseException;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Throwable;

/**
 * Base class for generated endpoints sharing the same request/response logic.
 * Concrete endpoints should define PATH_TEMPLATE and METHOD and may override
 * parseResponse if they need custom parsing.
 */
abstract class AbstractEndpoint
{
    /** @var string HTTP method (GET, POST, DELETE, ...) */
    protected const METHOD = 'GET';

    /** @var string Endpoint URL template, e.g. '/admin/api/tags.{_format}' */
    protected const PATH_TEMPLATE = '';

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly SerializerInterface $serializer,
        private readonly RequestAuthenticatorInterface $authenticator,
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        $method = static::METHOD;
        $path = static::PATH_TEMPLATE;
        foreach ($parameters as $k => $v) {
            $path = str_replace('{'.$k.'}', (string) $v, $path);
        }
        if ($query) {
            $qs = http_build_query($query);
            $path .= (str_contains($path, '?') ? '&' : '?') . $qs;
        }

        $request = $this->requestFactory
            ->createRequest($method, rtrim($this->baseUrl, '/') . $path)
            ->withHeader('Accept', 'application/json');

        // When needed, concrete endpoints can override to handle body.
        // We leave $body unused here to keep compatibility with current endpoints.

        $request = $this->authenticator->authenticate($request);

        try {
            return $this->http->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            // Wrap PSR-18 client exceptions (network errors, timeouts, etc.)
            throw new TransportException('HTTP client error: ' . $e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            // Any other unexpected error during the request lifecycle
            throw new UnexpectedResponseException('Unexpected error during request: ' . $e->getMessage(), 0, $e);
        }
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');

        $data = null;
        if ($body !== '' && stripos($contentType, 'application/json') !== false) {
            try {
                $data = $this->serializer->deserialize($body, 'json');
            } catch (Throwable $e) {
                // If the response claims JSON but is invalid, surface a clear error
                throw new InvalidJsonException('Invalid JSON response body', $status, $e);
            }
        }

        // Success responses (2xx)
        if ($status >= 200 && $status < 300) {
            // 204/205 No Content: return null consistently
            if ($status === 204 || $status === 205) {
                return null;
            }
            // Return parsed data when available, otherwise raw body for non-JSON success
            return $data !== null ? $data : ($body !== '' ? $body : null);
        }

        // Specific client errors
        if ($status === 400) {
            $errors = is_array($data) ? $data : null;
            throw new ValidationException('Bad Request', 400, null, $errors);
        }
        if ($status === 401) {
            throw new UnauthorizedException('Unauthorized', 401, null, is_array($data) ? $data : null);
        }
        if ($status === 403) {
            throw new ForbiddenException('Forbidden', 403, null, is_array($data) ? $data : null);
        }
        if ($status === 404) {
            throw new NotFoundException('Resource not found', 404);
        }
        if ($status === 405) {
            throw new MethodNotAllowedException('Method Not Allowed', 405, null, is_array($data) ? $data : null);
        }
        if ($status === 409) {
            throw new ConflictException('Conflict', 409, null, is_array($data) ? $data : null);
        }
        if ($status === 412) {
            throw new PreconditionFailedException('Precondition Failed', 412, null, is_array($data) ? $data : null);
        }
        if ($status === 415) {
            throw new UnsupportedMediaTypeException('Unsupported Media Type', 415, null, is_array($data) ? $data : null);
        }
        if ($status === 422) {
            $errors = is_array($data) ? $data : null;
            throw new ValidationException('Validation error', 422, null, $errors);
        }
        if ($status === 429) {
            $retryAfter = $response->getHeaderLine('Retry-After');
            $msg = 'Too Many Requests' . ($retryAfter !== '' ? ('; retry after ' . $retryAfter) : '');
            throw new TooManyRequestsException($msg, 429, null, is_array($data) ? $data : null);
        }

        // Redirection not expected in API client usage
        if ($status >= 300 && $status < 400) {
            throw new RedirectionException('Unexpected redirection', $status, null, is_array($data) ? $data : null);
        }

        // Server errors
        if ($status >= 500 && $status < 600) {
            $message = 'Server error';
            if (is_array($data) && isset($data['message'])) {
                $message = (string) $data['message'];
            } elseif ($body !== '' && $data === null) {
                $message = 'Server error: ' . substr($body, 0, 2000);
            }
            throw new ServerErrorException($message, $status, null, is_array($data) ? $data : null);
        }

        // Fallback for any other unexpected status
        $message = is_array($data) && isset($data['message']) ? (string) $data['message'] : 'API error';
        throw new UnexpectedResponseException($message, $status, null, is_array($data) ? $data : null);
    }
}
