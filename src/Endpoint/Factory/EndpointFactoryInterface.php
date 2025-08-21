<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint\Factory;

use Sulu\ApiClient\Endpoint\EndpointInterface;

/**
 * Factory interface for creating endpoint instances with proper dependency injection.
 */
interface EndpointFactoryInterface
{
    /**
     * Create an endpoint instance with all required dependencies.
     *
     * @template T of EndpointInterface
     * @param class-string<T> $endpointClass
     * @return EndpointInterface
     *
     * @throws \InvalidArgumentException if the class does not implement EndpointInterface
     */
    public function create(string $endpointClass): EndpointInterface;
}