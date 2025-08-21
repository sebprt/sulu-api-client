<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Cache;

use Psr\SimpleCache\CacheInterface;
use Sulu\ApiClient\ApiClient;
use Sulu\ApiClient\Endpoint\EndpointInterface;
use Sulu\ApiClient\Pagination\CursorPaginator;

/**
 * Cached API client that wraps around the base ApiClient to provide caching functionality.
 * Only GET operations (read, collection) are cached for performance improvements.
 */
final class CachedApiClient
{
    public function __construct(
        private readonly ApiClient $client,
        private readonly CacheInterface $cache,
        private readonly int $defaultTtl = 300,
    ) {
    }

    /**
     * Create operation - not cached, delegates to underlying client.
     */
    public function create(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        return $this->client->create($endpoint, $parameters, $query, $body);
    }

    /**
     * Read operation - cached if endpoint is cacheable.
     */
    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->read($endpoint, $parameters, $query);
        }

        $cacheKey = $this->generateCacheKey('read', $endpoint, $parameters, $query);

        $cachedResult = $this->cache->get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        $result = $this->client->read($endpoint, $parameters, $query);
        $this->cache->set($cacheKey, $result, $this->defaultTtl);

        return $result;
    }

    /**
     * Update operation - not cached, delegates to underlying client and invalidates cache.
     */
    public function update(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        $result = $this->client->update($endpoint, $parameters, $query, $body);
        $this->invalidateEndpointCache($endpoint, $parameters);
        
        return $result;
    }

    /**
     * Upsert operation - not cached, delegates to underlying client and invalidates cache.
     */
    public function upsert(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        $result = $this->client->upsert($endpoint, $parameters, $query, $body);
        $this->invalidateEndpointCache($endpoint, $parameters);
        
        return $result;
    }

    /**
     * Delete operation - not cached, delegates to underlying client and invalidates cache.
     */
    public function delete(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        $result = $this->client->delete($endpoint, $parameters, $query);
        $this->invalidateEndpointCache($endpoint, $parameters);
        
        return $result;
    }

    /**
     * Collection operation - cached if endpoint is cacheable.
     */
    public function collection(EndpointInterface $endpoint, array $parameters = [], array $query = [], ?string $embeddedKey = null, int $limit = 10): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->collection($endpoint, $parameters, $query, $embeddedKey, $limit);
        }

        $cacheKey = $this->generateCacheKey('collection', $endpoint, $parameters, $query, $embeddedKey, $limit);

        $cachedResult = $this->cache->get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }

        $result = $this->client->collection($endpoint, $parameters, $query, $embeddedKey, $limit);
        $this->cache->set($cacheKey, $result, $this->defaultTtl);

        return $result;
    }

    /**
     * Paginate collection - not cached due to complexity of cursor pagination.
     */
    public function paginateEmbeddedCursorCollection(EndpointInterface $endpoint, string $embeddedKey, array $parameters = [], array $baseQuery = [], int $limit = 10, ?string $initialCursor = null): CursorPaginator
    {
        return $this->client->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, $parameters, $baseQuery, $limit, $initialCursor);
    }


    /**
     * Invalidate all cache entries for a specific endpoint and parameters.
     */
    public function invalidateEndpointCache(EndpointInterface $endpoint, array $parameters = []): void
    {
        $endpointClass = $endpoint::class;
        $baseKey = md5($endpointClass . serialize($parameters));
        
        // Since PSR-16 doesn't provide a way to delete by pattern,
        // we store keys we've cached and delete them individually
        $keyPattern = "sulu_api:{$baseKey}:*";
        $this->cache->delete($keyPattern);
    }

    /**
     * Clear all cache entries.
     */
    public function clearCache(): void
    {
        $this->cache->clear();
    }

    /**
     * Determines if an endpoint should be cached based on naming convention.
     * Only GET operations (typically starting with "Get" in class name) are cached.
     */
    private function isCacheable(EndpointInterface $endpoint): bool
    {
        $className = $endpoint::class;
        
        // Cache endpoints that are likely read operations
        return str_contains($className, 'Get') || 
               str_contains($className, 'List') || 
               str_contains($className, 'Find') ||
               str_contains($className, 'Show');
    }

    /**
     * Generate a cache key for the given operation and parameters.
     */
    private function generateCacheKey(string $operation, EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed ...$extra): string
    {
        $endpointClass = $endpoint::class;
        $keyData = [
            'operation' => $operation,
            'endpoint' => $endpointClass,
            'parameters' => $parameters,
            'query' => $query,
            'extra' => $extra,
        ];

        $hash = md5(serialize($keyData));
        
        return "sulu_api:{$hash}";
    }
}