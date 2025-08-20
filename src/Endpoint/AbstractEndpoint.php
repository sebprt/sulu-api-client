<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
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
use Sulu\ApiClient\Serializer\SerializerInterface;

/**
 * Base class for generated endpoints sharing the same request/response logic.
 * Concrete endpoints should define PATH_TEMPLATE and METHOD and may override
 * parseResponse if they need custom parsing.
 */
abstract class AbstractEndpoint implements EndpointInterface
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
     * Default content type when serializing non-stream bodies.
     * Subclasses can override to change default.
     */
    protected function defaultContentType(): string
    {
        return 'application/json';
    }

    /**
     * Helper to attach a JSON body using the endpoint serializer.
     * Note: GET/HEAD requests must not include a body per RFC and will be ignored.
     */
    protected function withJsonBody(\Psr\Http\Message\RequestInterface $request, mixed $body): \Psr\Http\Message\RequestInterface
    {
        $method = strtoupper($request->getMethod());
        if ('GET' === $method || 'HEAD' === $method) {
            return $request;
        }

        if ($body instanceof StreamInterface) {
            return $request->withBody($body);
        }

        $payload = $this->serializer->serialize($body, 'json');
        $stream = $request->getBody();
        if ($stream->isWritable()) {
            $stream->write($payload);
        }

        return $request->withHeader('Content-Type', $this->defaultContentType());
    }

    private function isJsonContentType(string $contentType): bool
    {
        $media = strtolower(trim(explode(';', $contentType)[0]));

        return 'application/json' === $media || 'application/problem+json' === $media || str_ends_with($media, '+json');
    }

    private function messageFromProblemJson(string $default, mixed $data): string
    {
        if (is_array($data)) {
            $title = isset($data['title']) ? (string) $data['title'] : null;
            $detail = isset($data['detail']) ? (string) $data['detail'] : null;
            if (null !== $title && null !== $detail && '' !== $title && '' !== $detail) {
                return $title.': '.$detail;
            }
            if (null !== $title && '' !== $title) {
                return $title;
            }
            if (null !== $detail && '' !== $detail) {
                return $detail;
            }
        }

        return $default;
    }

    /**
     * Build and send the HTTP request.
     *
     * - GET/HEAD: any provided $body will be ignored to remain idempotent and RFC-compliant.
     * - Other methods: if $body is a StreamInterface, it's used as-is; otherwise it is serialized
     *   via SerializerInterface (default: JSON) and Content-Type is set to defaultContentType().
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $query
     */
    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        $method = static::METHOD;
        $path = static::PATH_TEMPLATE;
        foreach ($parameters as $k => $v) {
            if (!is_scalar($v) && !$v instanceof \Stringable && null !== $v) {
                // Skip non-stringable values to avoid invalid path substitution
                continue;
            }
            $path = str_replace('{'.$k.'}', (string) $v, $path);
        }
        if ($query) {
            $qs = http_build_query($query);
            $path .= (str_contains($path, '?') ? '&' : '?').$qs;
        }

        $request = $this->requestFactory
            ->createRequest($method, rtrim($this->baseUrl, '/').$path)
            ->withHeader('Accept', 'application/json');

        // Attach body if applicable
        $upper = strtoupper($method);
        if (null !== $body && 'GET' !== $upper && 'HEAD' !== $upper) {
            if ($body instanceof StreamInterface) {
                $request = $request->withBody($body);
            } else {
                $payload = $this->serializer->serialize($body, 'json');
                $stream = $request->getBody();
                if ($stream->isWritable()) {
                    $stream->write($payload);
                }
                // Only set Content-Type if not already provided by the caller
                if ('' === $request->getHeaderLine('Content-Type')) {
                    $request = $request->withHeader('Content-Type', $this->defaultContentType());
                }
            }
        }

        $request = $this->authenticator->authenticate($request);

        try {
            return $this->http->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            // Wrap PSR-18 client exceptions (network errors, timeouts, etc.)
            throw new TransportException('HTTP client error: '.$e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            // Any other unexpected error during the request lifecycle
            throw new UnexpectedResponseException('Unexpected error during request: '.$e->getMessage(), 0, $e);
        }
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');

        $data = null;
        $isJson = $this->isJsonContentType($contentType);
        if ('' !== $body && $isJson) {
            try {
                $data = $this->serializer->deserialize($body, 'json');
            } catch (\Throwable $e) {
                $preview = substr($body, 0, 2000);
                $msg = 'Invalid JSON response body (status '.$status.')';
                if ('' !== $preview) {
                    $msg .= '; preview: '.$preview;
                }
                throw new InvalidJsonException($msg, $status, $e);
            }
        }

        // Success responses (2xx)
        if ($status >= 200 && $status < 300) {
            // 204/205 No Content: return null consistently
            if (204 === $status || 205 === $status) {
                return null;
            }
            // Return parsed data when available, otherwise raw body for non-JSON success
            if (null !== $data) {
                return $data;
            }
            if ('' === $body) {
                return null;
            }

            return $body;
        }

        // Specific client errors
        if (400 === $status) {
            $errors = is_array($data) ? $data : null;
            $message = $this->messageFromProblemJson('Bad Request', $data);
            throw new ValidationException($message, 400, null, $errors);
        }
        if (401 === $status) {
            $message = $this->messageFromProblemJson('Unauthorized', $data);
            throw new UnauthorizedException($message, 401, null, is_array($data) ? $data : null);
        }
        if (403 === $status) {
            $message = $this->messageFromProblemJson('Forbidden', $data);
            throw new ForbiddenException($message, 403, null, is_array($data) ? $data : null);
        }
        if (404 === $status) {
            $message = $this->messageFromProblemJson('Resource not found', $data);
            throw new NotFoundException($message, 404, null, is_array($data) ? $data : null);
        }
        if (405 === $status) {
            $message = $this->messageFromProblemJson('Method Not Allowed', $data);
            throw new MethodNotAllowedException($message, 405, null, is_array($data) ? $data : null);
        }
        if (409 === $status) {
            $message = $this->messageFromProblemJson('Conflict', $data);
            throw new ConflictException($message, 409, null, is_array($data) ? $data : null);
        }
        if (412 === $status) {
            $message = $this->messageFromProblemJson('Precondition Failed', $data);
            throw new PreconditionFailedException($message, 412, null, is_array($data) ? $data : null);
        }
        if (415 === $status) {
            $message = $this->messageFromProblemJson('Unsupported Media Type', $data);
            throw new UnsupportedMediaTypeException($message, 415, null, is_array($data) ? $data : null);
        }
        if (422 === $status) {
            $errors = is_array($data) ? $data : null;
            $message = $this->messageFromProblemJson('Validation error', $data);
            throw new ValidationException($message, 422, null, $errors);
        }
        if (429 === $status) {
            $retryAfter = $response->getHeaderLine('Retry-After');
            $base = $this->messageFromProblemJson('Too Many Requests', $data);
            $msg = $base.('' !== $retryAfter ? ('; retry after '.$retryAfter) : '');
            throw new TooManyRequestsException($msg, 429, null, is_array($data) ? $data : null);
        }

        // Redirection not expected in API client usage
        if ($status >= 300 && $status < 400) {
            $message = $this->messageFromProblemJson('Unexpected redirection', $data);
            throw new RedirectionException($message, $status, null, is_array($data) ? $data : null);
        }

        // Server errors
        if ($status >= 500 && $status < 600) {
            $message = $this->messageFromProblemJson('Server error', $data);
            if ('Server error' === $message && is_array($data) && isset($data['message'])) {
                $message = (string) $data['message'];
            } elseif ('' !== $body && null === $data) {
                $message = 'Server error: '.substr($body, 0, 2000);
            }
            throw new ServerErrorException($message, $status, null, is_array($data) ? $data : null);
        }

        // Fallback for any other unexpected status
        $message = $this->messageFromProblemJson('API error', $data);
        if ('API error' === $message && is_array($data) && isset($data['message'])) {
            $message = (string) $data['message'];
        }
        throw new UnexpectedResponseException($message, $status, null, is_array($data) ? $data : null);
    }
}
