<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface;
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
        private readonly ContentTypeMatcherInterface $contentTypeMatcher,
        private readonly string $baseUrl,
    ) {
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
                    $request = $request->withHeader('Content-Type', 'application/json');
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
        $isJson = $this->contentTypeMatcher->isJson($contentType);
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

        $status = $response->getStatusCode();
        if (400 === $status) {
            throw new ValidationException('Bad Request', $status);
        }

        if (401 === $status) {
            throw new UnauthorizedException('Unauthorized', $status);
        }

        if (403 === $status) {
            throw new ForbiddenException('Forbidden', $status);
        }

        if (404 === $status) {
            throw new NotFoundException('Resource not found', $status);
        }

        if (405 === $status) {
            throw new MethodNotAllowedException('Method Not Allowed', $status);
        }

        if (409 === $status) {
            throw new ConflictException('Conflict', $status);
        }

        if (412 === $status) {
            throw new PreconditionFailedException('Precondition Failed', $status);
        }

        if (415 === $status) {
            throw new UnsupportedMediaTypeException('Unsupported Media Type', $status);
        }

        if (422 === $status) {
            throw new ValidationException('Validation error', $status);
        }

        if (429 === $status) {
            throw new TooManyRequestsException('Too Many Requests. Retry later', 429);
        }

        if ($status >= 300 && $status < 400) {
            throw new RedirectionException('Unexpected redirection', $status);
        }

        if ($status >= 500 && $status < 600) {
            throw new ServerErrorException('Server error', $status);
        }

        throw new ApiException( 'API error', $status);
    }
}
