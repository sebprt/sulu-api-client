<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Exception\ApiException;
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Serializer\SerializerInterface;

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
        return $this->http->sendRequest($request);
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = $body !== '' ? $this->serializer->deserialize($body, 'json') : null;

        if ($status >= 200 && $status < 300) {
            return $data;
        }
        if ($status === 404) {
            throw new NotFoundException('Resource not found', 404);
        }
        if ($status === 422 || $status === 400) {
            $errors = is_array($data) ? $data : null;
            throw new ValidationException('Validation error', $status, null, $errors);
        }

        $message = is_array($data) && isset($data['message']) ? (string) $data['message'] : 'API error';
        throw new ApiException($message, $status);
    }
}
