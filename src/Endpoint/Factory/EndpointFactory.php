<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint\Factory;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface;
use Sulu\ApiClient\Serializer\SerializerInterface;

/**
 * Factory for creating endpoint instances with proper dependency injection.
 * Replaces direct instantiation with a more flexible factory pattern.
 */
final readonly class EndpointFactory implements EndpointFactoryInterface
{
    public function __construct(
        private HttpClientInterface $http,
        private RequestFactoryInterface $requestFactory,
        private SerializerInterface $serializer,
        private RequestAuthenticatorInterface $authenticator,
        private ContentTypeMatcherInterface $contentTypeMatcher,
        private string $baseUrl,
    ) {
    }

    /**
     * Create an endpoint instance with all required dependencies.
     *
     * @template T of EndpointInterface
     * @param class-string<T> $endpointClass
     * @return EndpointInterface
     *
     * @throws \InvalidArgumentException if the class does not implement EndpointInterface
     */
    public function create(string $endpointClass): EndpointInterface
    {
        if (!is_subclass_of($endpointClass, EndpointInterface::class)) {
            throw new \InvalidArgumentException("Class {$endpointClass} must implement EndpointInterface");
        }

        return new $endpointClass(
            $this->http,
            $this->requestFactory,
            $this->serializer,
            $this->authenticator,
            $this->contentTypeMatcher,
            $this->baseUrl,
        );
    }
}