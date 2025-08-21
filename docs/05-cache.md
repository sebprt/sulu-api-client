# Cache

## Introduction au cache

Le client API Sulu propose un système de mise en cache intégré via la classe `CachedApiClient`. Ce wrapper autour du `ApiClient` de base permet d'améliorer significativement les performances en mettant en cache les opérations de lecture (GET) tout en maintenant la cohérence des données lors des opérations d'écriture.

## Architecture du cache

Le système de cache utilise l'interface PSR-16 SimpleCache, ce qui garantit la compatibilité avec de nombreuses implémentations de cache disponibles dans l'écosystème PHP.

### Principe de fonctionnement

- **Opérations en cache** : `read()` et `collection()` (opérations de lecture uniquement)
- **Opérations non cachées** : `create()`, `update()`, `upsert()`, `delete()` (opérations d'écriture)
- **Invalidation automatique** : Les opérations d'écriture invalident automatiquement le cache associé
- **Pagination** : La pagination par curseur n'est pas mise en cache en raison de sa complexité

## Configuration de base

### Installation des dépendances cache

Le client nécessite une implémentation PSR-16. Voici quelques options populaires :

```bash
# Symfony Cache (recommandé)
composer require symfony/cache

# Cache simple en mémoire (pour les tests)
composer require cache/simple-cache-bridge cache/array-adapter

# Redis
composer require predis/predis cache/predis-adapter

# Memcached  
composer require cache/memcached-adapter
```

### Configuration simple avec Symfony Cache

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Sulu\ApiClient\Cache\CachedApiClient;

// Adaptateur de cache (fichiers)
$cacheAdapter = new FilesystemAdapter('sulu_api', 0, '/tmp/cache');

// Interface PSR-16
$cache = new Psr16Cache($cacheAdapter);

// Client API de base
$apiClient = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $authenticator,
    $baseUrl,
    $contentTypeMatcher
);

// Client avec cache (TTL par défaut : 300 secondes = 5 minutes)
$cachedClient = new CachedApiClient($apiClient, $cache, 300);
```

### Configuration avec Redis

```php
use Predis\Client as RedisClient;
use Cache\Adapter\Predis\PredisAdapter;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

// Client Redis
$redisClient = new RedisClient(['host' => '127.0.0.1', 'port' => 6379]);

// Adaptateur cache
$cacheAdapter = new PredisAdapter($redisClient);
$cache = new SimpleCacheBridge($cacheAdapter);

// Client avec cache Redis
$cachedClient = new CachedApiClient($apiClient, $cache, 600); // 10 minutes
```

## Utilisation du cache

### Opérations automatiquement mises en cache

```php
use Sulu\ApiClient\Endpoint\SuluGetContactEndpoint;
use Sulu\ApiClient\Endpoint\SuluGetContactsEndpoint;

$endpointFactory = $cachedClient->getEndpointFactory();

// Cette requête sera mise en cache
$contactEndpoint = $endpointFactory->create(SuluGetContactEndpoint::class);
$contact = $cachedClient->read($contactEndpoint, ['id' => 123]);

// Deuxième appel identique - récupéré depuis le cache
$contactCached = $cachedClient->read($contactEndpoint, ['id' => 123]);

// Collection également mise en cache
$contactsEndpoint = $endpointFactory->create(SuluGetContactsEndpoint::class);
$contacts = $cachedClient->collection($contactsEndpoint, [], ['limit' => 20]);
```

### Opérations avec invalidation automatique

```php
use Sulu\ApiClient\Endpoint\SuluPutContactEndpoint;

// Mise à jour d'un contact
$updateEndpoint = $endpointFactory->create(SuluPutContactEndpoint::class);
$updatedContact = $cachedClient->update(
    $updateEndpoint,
    ['id' => 123],
    [],
    ['firstName' => 'Jean', 'lastName' => 'Martin']
);

// Le cache pour ce contact est automatiquement invalidé
// Le prochain appel read() fera une nouvelle requête HTTP
$freshContact = $cachedClient->read($contactEndpoint, ['id' => 123]);
```

## Gestion manuelle du cache

### Invalidation sélective

```php
// Invalider le cache pour un endpoint et des paramètres spécifiques
$cachedClient->invalidateEndpointCache($contactEndpoint, ['id' => 123]);

// Invalider tout le cache pour un type d'endpoint
$cachedClient->invalidateEndpointCache($contactEndpoint);
```

### Vidage complet du cache

```php
// Vider tout le cache
$cachedClient->clearCache();
```

## Détection automatique des endpoints cachables

Le `CachedApiClient` détermine automatiquement quels endpoints doivent être mis en cache en analysant le nom de la classe :

```php
// Endpoints automatiquement mis en cache (opérations de lecture)
class SuluGetContactEndpoint { } // ✅ Cachable (contient "Get")
class SuluGetContactsEndpoint { } // ✅ Cachable (contient "Get") 
class SuluListArticlesEndpoint { } // ✅ Cachable (contient "List")
class SuluFindUsersEndpoint { } // ✅ Cachable (contient "Find")
class SuluShowPageEndpoint { } // ✅ Cachable (contient "Show")

// Endpoints non mis en cache (opérations d'écriture)
class SuluPostContactEndpoint { } // ❌ Non cachable
class SuluPutContactEndpoint { } // ❌ Non cachable
class SuluDeleteContactEndpoint { } // ❌ Non cachable
class CustomCreateUserEndpoint { } // ❌ Non cachable
```

## Configuration avancée

### Client avec TTL personnalisés par endpoint

```php
class SmartCachedApiClient extends CachedApiClient
{
    private array $endpointTtl = [
        'SuluGetContactEndpoint' => 900,      // 15 minutes pour les contacts
        'SuluGetAccountEndpoint' => 1800,     // 30 minutes pour les comptes
        'SuluGetPageEndpoint' => 3600,        // 1 heure pour les pages
        'SuluGetArticlesEndpoint' => 300,     // 5 minutes pour les listes d'articles
    ];

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
        
        // TTL personnalisé selon l'endpoint
        $ttl = $this->getEndpointTtl($endpoint);
        $this->cache->set($cacheKey, $result, $ttl);

        return $result;
    }

    private function getEndpointTtl(EndpointInterface $endpoint): int
    {
        $className = $endpoint::class;
        $shortName = substr(strrchr($className, '\\'), 1);
        
        return $this->endpointTtl[$shortName] ?? $this->defaultTtl;
    }
}
```

### Cache avec compression

```php
class CompressedCacheApiClient extends CachedApiClient
{
    protected function generateCacheKey(string $operation, EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed ...$extra): string
    {
        // Clé de base
        $baseKey = parent::generateCacheKey($operation, $endpoint, $parameters, $query, ...$extra);
        
        // Ajouter un préfixe pour la version compressée
        return 'compressed:' . $baseKey;
    }

    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->read($endpoint, $parameters, $query);
        }

        $cacheKey = $this->generateCacheKey('read', $endpoint, $parameters, $query);
        
        $compressedData = $this->cache->get($cacheKey);
        if ($compressedData !== null) {
            // Décompresser les données
            $jsonData = gzuncompress($compressedData);
            return json_decode($jsonData, true);
        }

        $result = $this->client->read($endpoint, $parameters, $query);
        
        // Compresser avant mise en cache
        $jsonData = json_encode($result);
        $compressedData = gzcompress($jsonData, 6);
        
        $this->cache->set($cacheKey, $compressedData, $this->defaultTtl);

        return $result;
    }
}
```

## Stratégies de cache avancées

### Cache avec tags (Symfony Cache)

```php
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class TaggedCacheApiClient extends CachedApiClient
{
    private TagAwareAdapter $taggedCache;

    public function __construct(
        ApiClient $client,
        TagAwareAdapter $cache,
        int $defaultTtl = 300
    ) {
        parent::__construct($client, $cache, $defaultTtl);
        $this->taggedCache = $cache;
    }

    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->read($endpoint, $parameters, $query);
        }

        $cacheKey = $this->generateCacheKey('read', $endpoint, $parameters, $query);
        $tags = $this->generateTags($endpoint, $parameters);
        
        $cacheItem = $this->taggedCache->getItem($cacheKey);
        
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->client->read($endpoint, $parameters, $query);
        
        $cacheItem->set($result);
        $cacheItem->expiresAfter($this->defaultTtl);
        $cacheItem->tag($tags);
        
        $this->taggedCache->save($cacheItem);

        return $result;
    }

    private function generateTags(EndpointInterface $endpoint, array $parameters): array
    {
        $tags = [];
        
        // Tag par type d'endpoint
        $endpointClass = $endpoint::class;
        $tags[] = 'endpoint:' . $endpointClass;
        
        // Tags par entité (si ID présent)
        if (isset($parameters['id'])) {
            $entityName = $this->extractEntityName($endpointClass);
            $tags[] = "entity:{$entityName}:{$parameters['id']}";
            $tags[] = "entity:{$entityName}";
        }
        
        return $tags;
    }

    private function extractEntityName(string $endpointClass): string
    {
        // Extraire le nom de l'entité depuis la classe endpoint
        // SuluGetContactEndpoint -> contact
        preg_match('/Sulu(?:Get|Post|Put|Delete)(\w+?)(?:s)?Endpoint/', $endpointClass, $matches);
        return strtolower($matches[1] ?? 'unknown');
    }

    public function invalidateByTag(string $tag): void
    {
        $this->taggedCache->invalidateTags([$tag]);
    }

    public function update(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        $result = $this->client->update($endpoint, $parameters, $query, $body);
        
        // Invalidation par tags
        if (isset($parameters['id'])) {
            $entityName = $this->extractEntityName($endpoint::class);
            $this->invalidateByTag("entity:{$entityName}:{$parameters['id']}");
            $this->invalidateByTag("entity:{$entityName}");
        }
        
        return $result;
    }
}
```

### Cache avec warm-up

```php
class WarmUpCacheService
{
    private CachedApiClient $cachedClient;
    private array $warmUpEndpoints;

    public function __construct(CachedApiClient $cachedClient, array $warmUpEndpoints)
    {
        $this->cachedClient = $cachedClient;
        $this->warmUpEndpoints = $warmUpEndpoints;
    }

    public function warmUpCache(): void
    {
        $endpointFactory = $this->cachedClient->getEndpointFactory();

        foreach ($this->warmUpEndpoints as $endpointConfig) {
            try {
                $endpoint = $endpointFactory->create($endpointConfig['class']);
                
                if (isset($endpointConfig['parameters'])) {
                    // Warm-up d'entités spécifiques
                    foreach ($endpointConfig['parameters'] as $params) {
                        $this->cachedClient->read($endpoint, $params);
                    }
                } else {
                    // Warm-up de collections
                    $this->cachedClient->collection(
                        $endpoint,
                        [],
                        $endpointConfig['query'] ?? []
                    );
                }
                
                echo "✓ Cache réchauffé pour " . $endpointConfig['class'] . "\n";
                
            } catch (\Exception $e) {
                echo "✗ Erreur lors du réchauffement de " . $endpointConfig['class'] . ": " . $e->getMessage() . "\n";
            }
        }
    }
}

// Configuration du warm-up
$warmUpEndpoints = [
    [
        'class' => SuluGetContactsEndpoint::class,
        'query' => ['limit' => 50]
    ],
    [
        'class' => SuluGetAccountsEndpoint::class,
        'query' => ['limit' => 30]
    ],
    [
        'class' => SuluGetContactEndpoint::class,
        'parameters' => [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ]
    ]
];

$warmUpService = new WarmUpCacheService($cachedClient, $warmUpEndpoints);
$warmUpService->warmUpCache();
```

## Monitoring et métriques de cache

### Wrapper avec métriques

```php
class MetricsCachedApiClient extends CachedApiClient
{
    private array $metrics = [
        'hits' => 0,
        'misses' => 0,
        'writes' => 0,
        'invalidations' => 0
    ];

    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->read($endpoint, $parameters, $query);
        }

        $cacheKey = $this->generateCacheKey('read', $endpoint, $parameters, $query);

        $cachedResult = $this->cache->get($cacheKey);
        if ($cachedResult !== null) {
            $this->metrics['hits']++;
            return $cachedResult;
        }

        $this->metrics['misses']++;
        $result = $this->client->read($endpoint, $parameters, $query);
        $this->cache->set($cacheKey, $result, $this->defaultTtl);
        $this->metrics['writes']++;

        return $result;
    }

    public function invalidateEndpointCache(EndpointInterface $endpoint, array $parameters = []): void
    {
        parent::invalidateEndpointCache($endpoint, $parameters);
        $this->metrics['invalidations']++;
    }

    public function getMetrics(): array
    {
        return $this->metrics + [
            'hit_rate' => $this->getHitRate(),
            'total_operations' => $this->getTotalOperations()
        ];
    }

    private function getHitRate(): float
    {
        $total = $this->metrics['hits'] + $this->metrics['misses'];
        return $total > 0 ? ($this->metrics['hits'] / $total) * 100 : 0;
    }

    private function getTotalOperations(): int
    {
        return $this->metrics['hits'] + $this->metrics['misses'];
    }

    public function printStats(): void
    {
        $metrics = $this->getMetrics();
        
        echo "=== Statistiques de cache ===\n";
        echo "Hits: {$metrics['hits']}\n";
        echo "Misses: {$metrics['misses']}\n";
        echo "Taux de réussite: " . number_format($metrics['hit_rate'], 2) . "%\n";
        echo "Écritures: {$metrics['writes']}\n";
        echo "Invalidations: {$metrics['invalidations']}\n";
        echo "Total opérations: {$metrics['total_operations']}\n";
    }
}
```

## Tests avec cache

### Mock du cache pour les tests

```php
<?php

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class CachedApiClientTest extends TestCase
{
    public function testCacheHit(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $apiClient = $this->createMock(ApiClient::class);
        $endpoint = $this->createMock(EndpointInterface::class);

        $cachedData = ['id' => 123, 'name' => 'Test'];
        
        // Le cache retourne des données
        $cache->expects($this->once())
              ->method('get')
              ->willReturn($cachedData);

        // L'API client ne doit pas être appelé
        $apiClient->expects($this->never())
                  ->method('read');

        $cachedClient = new CachedApiClient($apiClient, $cache);
        $result = $cachedClient->read($endpoint, ['id' => 123]);

        $this->assertEquals($cachedData, $result);
    }

    public function testCacheMiss(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $apiClient = $this->createMock(ApiClient::class);
        $endpoint = $this->createMock(EndpointInterface::class);

        $apiData = ['id' => 123, 'name' => 'Test'];
        
        // Le cache ne retourne rien
        $cache->expects($this->once())
              ->method('get')
              ->willReturn(null);

        // Les données doivent être mises en cache
        $cache->expects($this->once())
              ->method('set')
              ->with($this->anything(), $apiData, 300);

        // L'API client doit être appelé
        $apiClient->expects($this->once())
                  ->method('read')
                  ->willReturn($apiData);

        $cachedClient = new CachedApiClient($apiClient, $cache, 300);
        $result = $cachedClient->read($endpoint, ['id' => 123]);

        $this->assertEquals($apiData, $result);
    }
}
```

## Bonnes pratiques pour le cache

### 1. Choix du TTL approprié

```php
// TTL selon le type de données
$ttlConfig = [
    'static_data' => 3600,      // 1 heure pour données statiques
    'user_data' => 300,         // 5 minutes pour données utilisateur
    'dynamic_lists' => 60,      // 1 minute pour listes dynamiques
    'search_results' => 30,     // 30 secondes pour résultats de recherche
];
```

### 2. Gestion de la mémoire

```php
// Limite de taille pour éviter l'explosion mémoire
class SizeLimitedCache implements CacheInterface
{
    private CacheInterface $innerCache;
    private int $maxItemSize;

    public function __construct(CacheInterface $innerCache, int $maxItemSize = 1048576) // 1MB
    {
        $this->innerCache = $innerCache;
        $this->maxItemSize = $maxItemSize;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $serialized = serialize($value);
        
        if (strlen($serialized) > $this->maxItemSize) {
            // Ne pas mettre en cache les items trop gros
            return false;
        }

        return $this->innerCache->set($key, $value, $ttl);
    }

    // Déléguer les autres méthodes...
}
```

### 3. Cache hiérarchique

```php
class TieredCacheApiClient extends CachedApiClient
{
    private CacheInterface $l1Cache; // Cache rapide (APCu, mémoire)
    private CacheInterface $l2Cache; // Cache persistant (Redis, fichiers)

    public function __construct(
        ApiClient $client,
        CacheInterface $l1Cache,
        CacheInterface $l2Cache,
        int $defaultTtl = 300
    ) {
        parent::__construct($client, $l1Cache, $defaultTtl);
        $this->l1Cache = $l1Cache;
        $this->l2Cache = $l2Cache;
    }

    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        if (!$this->isCacheable($endpoint)) {
            return $this->client->read($endpoint, $parameters, $query);
        }

        $cacheKey = $this->generateCacheKey('read', $endpoint, $parameters, $query);

        // Essayer L1 cache
        $result = $this->l1Cache->get($cacheKey);
        if ($result !== null) {
            return $result;
        }

        // Essayer L2 cache
        $result = $this->l2Cache->get($cacheKey);
        if ($result !== null) {
            // Remettre en L1 cache
            $this->l1Cache->set($cacheKey, $result, min(300, $this->defaultTtl));
            return $result;
        }

        // Aller chercher via l'API
        $result = $this->client->read($endpoint, $parameters, $query);
        
        // Mettre en cache dans les deux niveaux
        $this->l1Cache->set($cacheKey, $result, min(300, $this->defaultTtl));
        $this->l2Cache->set($cacheKey, $result, $this->defaultTtl);

        return $result;
    }
}
```

Continuez avec les [middlewares](06-middleware.md) pour découvrir les fonctionnalités de logging et retry automatique.