# Usage avancé et bonnes pratiques

## Architecture d'application complète

### Service Layer avec injection de dépendances

```php
// src/Service/SuluApiService.php
class SuluApiService
{
    private CachedApiClient $client;
    private EndpointFactoryInterface $endpointFactory;
    private LoggerInterface $logger;

    public function __construct(
        CachedApiClient $client,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->endpointFactory = $client->getEndpointFactory();
        $this->logger = $logger;
    }

    public function syncContact(array $contactData): array
    {
        try {
            $endpoint = $this->endpointFactory->create(SuluGetContactEndpoint::class);
            
            // Vérifier si le contact existe
            if (isset($contactData['id'])) {
                try {
                    $existing = $this->client->read($endpoint, ['id' => $contactData['id']]);
                    return $this->updateContact($contactData);
                } catch (NotFoundException $e) {
                    // Contact n'existe pas, le créer
                    return $this->createContact($contactData);
                }
            }
            
            return $this->createContact($contactData);
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la synchronisation du contact', [
                'contact_data' => $contactData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

### Configuration Symfony complète

```yaml
# config/packages/sulu_api_client.yaml
parameters:
    sulu_api.base_url: '%env(SULU_API_BASE_URL)%'
    sulu_api.token: '%env(SULU_API_TOKEN)%'

services:
    # HTTP Client avec timeout personnalisé
    sulu_api.http_client:
        class: GuzzleHttp\Client
        arguments:
            -   timeout: 30
                connect_timeout: 10
                verify: '%kernel.debug%' # SSL verification en dev seulement

    # Cache Redis pour l'API
    sulu_api.cache:
        class: Symfony\Component\Cache\Adapter\RedisAdapter
        arguments:
            - '@redis.connection'
            - 'sulu_api'
            - 300 # TTL par défaut

    # Stack d'authentification avec middlewares
    sulu_api.authenticator:
        class: Sulu\ApiClient\Auth\BearerTokenAuthenticator
        arguments:
            $token: '%sulu_api.token%'

    sulu_api.logging_middleware:
        class: Sulu\ApiClient\Middleware\LoggingMiddleware
        arguments:
            $inner: '@sulu_api.authenticator'
            $logger: '@monolog.logger.sulu_api'

    # Client API principal
    Sulu\ApiClient\ApiClient:
        arguments:
            $http: '@sulu_api.http_client'
            $requestFactory: '@Psr\Http\Message\RequestFactoryInterface'
            $serializer: '@Sulu\ApiClient\Serializer\JsonSerializer'
            $authenticator: '@sulu_api.logging_middleware'
            $baseUrl: '%sulu_api.base_url%'
            $contentTypeMatcher: '@Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher'

    # Client avec cache
    Sulu\ApiClient\Cache\CachedApiClient:
        arguments:
            $client: '@Sulu\ApiClient\ApiClient'
            $cache: '@sulu_api.cache'
            $defaultTtl: 600

    # Services métier
    App\Service\SuluApiService:
        arguments:
            $client: '@Sulu\ApiClient\Cache\CachedApiClient'
            $logger: '@monolog.logger.sulu_api'
```

## Patterns d'intégration avancés

### Synchronisation bidirectionnelle

```php
class ContactSyncService
{
    private SuluApiService $suluApi;
    private ExternalApiService $externalApi;
    private EntityManagerInterface $em;

    public function syncBidirectional(): void
    {
        // 1. Sync Sulu -> External
        $this->syncSuluToExternal();
        
        // 2. Sync External -> Sulu
        $this->syncExternalToSulu();
    }

    private function syncSuluToExternal(): void
    {
        $endpoint = $this->suluApi->getEndpointFactory()->create(SuluGetContactsEndpoint::class);
        
        $paginator = $this->suluApi->paginateEmbeddedCursorCollection(
            $endpoint,
            'contacts',
            [],
            ['modified' => ['gte' => $this->getLastSyncTime()]],
            50
        );

        foreach ($paginator as $page) {
            foreach ($page->getItems() as $contact) {
                $this->externalApi->upsertContact($contact);
                $this->markAsSynced($contact['id']);
            }
        }
    }
}
```

### Event-driven architecture

```php
// Event Listener pour les modifications
class SuluContactListener
{
    private SuluApiService $suluApi;
    private EventDispatcherInterface $dispatcher;

    public function onContactModified(ContactModifiedEvent $event): void
    {
        try {
            $contact = $event->getContact();
            $suluContact = $this->suluApi->syncContact($contact->toArray());
            
            $this->dispatcher->dispatch(new ContactSyncedEvent($suluContact));
            
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(new ContactSyncFailedEvent($contact, $e));
        }
    }
}
```

## Optimisations de performance

### Pool de connexions

```php
class ConnectionPool
{
    private array $clients = [];
    private int $maxConnections;

    public function __construct(int $maxConnections = 5)
    {
        $this->maxConnections = $maxConnections;
    }

    public function getClient(): ApiClient
    {
        if (count($this->clients) < $this->maxConnections) {
            $this->clients[] = $this->createClient();
        }

        return array_shift($this->clients);
    }

    public function releaseClient(ApiClient $client): void
    {
        $this->clients[] = $client;
    }

    private function createClient(): ApiClient
    {
        // Configuration du client...
        return new ApiClient(/* ... */);
    }
}
```

### Batch processing optimisé

```php
class BatchProcessor
{
    private SuluApiService $api;
    private int $batchSize;

    public function processBatch(array $items): array
    {
        $results = [];
        $batches = array_chunk($items, $this->batchSize);

        foreach ($batches as $batchIndex => $batch) {
            $batchResults = [];
            
            foreach ($batch as $item) {
                try {
                    $batchResults[] = $this->processItem($item);
                } catch (\Exception $e) {
                    $this->logger->warning("Erreur batch {$batchIndex}", [
                        'item' => $item,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $results = array_merge($results, $batchResults);
            
            // Pause entre les batches
            if ($batchIndex > 0 && $batchIndex % 10 === 0) {
                usleep(500000); // 0.5 seconde
            }
        }

        return $results;
    }
}
```

## Monitoring et observabilité

### Métriques personnalisées

```php
class ApiMetricsCollector
{
    private array $metrics = [];

    public function recordRequest(string $endpoint, float $duration, int $statusCode): void
    {
        $this->metrics[] = [
            'endpoint' => $endpoint,
            'duration' => $duration,
            'status' => $statusCode,
            'timestamp' => microtime(true)
        ];
    }

    public function getStats(): array
    {
        $totalRequests = count($this->metrics);
        $totalDuration = array_sum(array_column($this->metrics, 'duration'));
        $errors = array_filter($this->metrics, fn($m) => $m['status'] >= 400);

        return [
            'total_requests' => $totalRequests,
            'average_duration' => $totalRequests > 0 ? $totalDuration / $totalRequests : 0,
            'error_rate' => $totalRequests > 0 ? count($errors) / $totalRequests * 100 : 0,
            'requests_per_second' => $this->calculateRps()
        ];
    }
}
```

## Gestion d'erreurs avancée

### Circuit breaker avec hystrix

```php
class HystrixCircuitBreaker
{
    private string $name;
    private int $threshold;
    private int $timeout;
    private int $windowSize;
    private array $requests = [];

    public function call(callable $callback): mixed
    {
        if ($this->isOpen()) {
            throw new CircuitOpenException("Circuit {$this->name} is open");
        }

        try {
            $result = $callback();
            $this->recordSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->recordFailure($e);
            throw $e;
        }
    }

    private function isOpen(): bool
    {
        $window = $this->getWindow();
        $failures = array_filter($window, fn($r) => !$r['success']);
        
        return count($failures) >= $this->threshold;
    }
}
```

## Tests d'intégration

### Test avec serveur mock

```php
class SuluApiIntegrationTest extends TestCase
{
    private MockWebServer $mockServer;
    private ApiClient $client;

    protected function setUp(): void
    {
        $this->mockServer = new MockWebServer();
        $this->mockServer->start();
        
        $this->client = new ApiClient(
            /* ... */,
            $this->mockServer->getBaseUrl()
        );
    }

    public function testContactFlow(): void
    {
        // Setup mock responses
        $this->mockServer->enqueue([
            new MockResponse(['id' => 123, 'name' => 'Test'], 201),
            new MockResponse(['id' => 123, 'name' => 'Updated'], 200)
        ]);

        // Test create
        $endpoint = new SuluPostContactEndpoint(/* ... */);
        $created = $this->client->create($endpoint, [], [], ['name' => 'Test']);
        $this->assertEquals(123, $created['id']);

        // Test update  
        $updateEndpoint = new SuluPutContactEndpoint(/* ... */);
        $updated = $this->client->update($updateEndpoint, ['id' => 123], [], ['name' => 'Updated']);
        $this->assertEquals('Updated', $updated['name']);
    }
}
```

## Troubleshooting

### Debug des requêtes

```php
class DebugMiddleware implements RequestAuthenticatorInterface
{
    public function authenticate(RequestInterface $request): RequestInterface
    {
        echo "=== DEBUG REQUEST ===\n";
        echo "Method: " . $request->getMethod() . "\n";
        echo "URI: " . $request->getUri() . "\n";
        echo "Headers:\n";
        
        foreach ($request->getHeaders() as $name => $values) {
            echo "  {$name}: " . implode(', ', $values) . "\n";
        }
        
        if ($request->getBody()->getSize() > 0) {
            echo "Body: " . $request->getBody()->getContents() . "\n";
        }
        
        echo "========================\n";
        
        return $this->inner->authenticate($request);
    }
}
```

### Résolution des problèmes courants

1. **Erreur 429 (Too Many Requests)**
```php
// Solution: Implémenter un retry avec backoff
$retryMiddleware = new RetryMiddleware(5, 1000, 30000);
```

2. **Timeout de connexion**
```php
// Solution: Augmenter les timeouts
$httpClient = new GuzzleClient(['timeout' => 60, 'connect_timeout' => 30]);
```

3. **Mémoire insuffisante avec pagination**
```php
// Solution: Traiter page par page sans accumulation
foreach ($paginator as $page) {
    foreach ($page->getItems() as $item) {
        processItem($item); // Traiter immédiatement
    }
    // Pas d'accumulation en mémoire
}
```

## Documentation et maintenance

### Génération automatique de documentation

```php
class EndpointDocGenerator
{
    public function generateDocs(): void
    {
        $reflection = new ReflectionClass(SuluGetContactEndpoint::class);
        $docComment = $reflection->getDocComment();
        
        // Parser et générer la documentation...
    }
}
```

Cette documentation complète couvre tous les aspects avancés du client API Sulu. Pour des questions spécifiques ou des cas d'usage particuliers, consultez le code source ou contactez l'équipe de développement.