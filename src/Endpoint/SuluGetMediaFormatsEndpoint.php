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

final class SuluGetMediaFormatsEndpoint
{
    public const OPERATION_ID = 'sulu-media-get-media-formats-get';

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly SerializerInterface $serializer,
        private readonly RequestAuthenticatorInterface $authenticator,
        private readonly string $baseUrl,
    ) {
    }

    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        // Skeleton request builder (non-functional)
        $method = 'GET';
        $pathTmpl = '/admin/api/media/{id}/formats.{_format}';
        $path = $pathTmpl;
        foreach ($parameters as $k => $v) {
            $path = str_replace("{".$k."}", (string)$v, $path);
        }
        if ($query) {
            $qs = http_build_query($query);
            $path .= (str_contains($path, "?") ? "&" : "?") . $qs;
        }
        $request = $this->requestFactory->createRequest($method, rtrim($this->baseUrl, "/") . $path)
            ->withHeader("Accept", "application/json");
        $request = $this->authenticator->authenticate($request);
        return $this->http->sendRequest($request);
    }

    public function parseResponse(ResponseInterface $response): mixed
    {
        $status = $response->getStatusCode();
        $body = (string)$response->getBody();
        $data = $body !== "" ? $this->serializer->deserialize($body, "json") : null;
        if ($status >= 200 && $status < 300) {
            return $data;
        }
        if ($status === 404) {
            throw new NotFoundException("Resource not found", 404);
        }
        if ($status === 422 || $status === 400) {
            $errors = is_array($data) ? $data : null;
            throw new ValidationException("Validation error", $status, null, $errors);
        }
        throw new ApiException("API error", $status);
    }
}
