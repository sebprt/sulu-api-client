# Middleware

## Introduction aux middlewares

Le client API Sulu utilise le pattern middleware pour ajouter des fonctionnalités transversales comme le logging et la retry automatique. Les middlewares s'intègrent parfaitement dans l'architecture d'authentification en implémentant l'interface `RequestAuthenticatorInterface`.

## Middleware de logging

Le `LoggingMiddleware` permet de journaliser automatiquement toutes les requêtes API avec sanitisation des en-têtes sensibles.

### Configuration de base

```php
use Sulu\ApiClient\Middleware\LoggingMiddleware;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;
use Psr\Log\LoggerInterface;

// Authenticator de base
$authenticator = new BearerTokenAuthenticator('votre-token');

// Logger (Monolog, par exemple)
$logger = new \Monolog\Logger('sulu-api');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('api.log'));

// Middleware de logging
$loggingAuthenticator = new LoggingMiddleware($authenticator, $logger);

// Client avec logging
$client = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $loggingAuthenticator, // Middleware wrappant l'authenticator
    $baseUrl,
    $contentTypeMatcher
);
```

### Fonctionnalités du LoggingMiddleware

- **Sanitisation automatique** : Les en-têtes `Authorization` et `Cookie` sont automatiquement supprimés des logs pour la sécurité
- **Logging structuré** : Utilise un format structuré compatible avec les analyseurs de logs
- **Performance tracking** : Peut être étendu pour mesurer les temps de réponse

### Exemple de logs générés

```
[2024-08-21 13:58:42] sulu-api.DEBUG: Requête API sortante {"method":"GET","uri":"https://api.sulu.io/contacts/123","headers":{"Content-Type":"application/json","User-Agent":"SuluApiClient/1.0"}} []

[2024-08-21 13:58:43] sulu-api.INFO: Réponse API reçue {"method":"GET","uri":"https://api.sulu.io/contacts/123","status":200,"response_time_ms":245} []
```

## Middleware de retry

Le `RetryMiddleware` fournit une fonctionnalité de retry automatique avec backoff exponentiel pour améliorer la résilience face aux erreurs temporaires.

### Configuration simple

```php
use Sulu\ApiClient\Middleware\RetryMiddleware;

// Configuration du retry
$retryMiddleware = new RetryMiddleware(
    maxRetries: 3,
    initialDelayMs: 100,
    maxDelayMs: 5000,
    backoffMultiplier: 2.0,
    jitter: true
);

// Pas besoin de wrapper un authenticator - le RetryMiddleware gère les requêtes directement
// Il s'intègre au niveau du client HTTP
```

### Configuration avancée

```php
// Retry avec conditions personnalisées
$retryMiddleware = new RetryMiddleware(
    maxRetries: 5,
    initialDelayMs: 200,
    maxDelayMs: 10000,
    backoffMultiplier: 1.5,
    jitter: true
);

// Définir quels codes de statut doivent déclencher un retry
$retryMiddleware->setRetryConditions([
    // Erreurs réseau/temporaires
    500, // Internal Server Error
    502, // Bad Gateway
    503, // Service Unavailable
    504, // Gateway Timeout
    429, // Too Many Requests
]);

// Exclure certains codes du retry
$retryMiddleware->setNonRetryableConditions([
    400, // Bad Request - erreur client, inutile de réessayer
    401, // Unauthorized
    403, // Forbidden
    404, // Not Found
]);
```

## Middleware de logging avec métriques

### LoggingMiddleware étendu

```php
class EnhancedLoggingMiddleware implements RequestAuthenticatorInterface
{
    private RequestAuthenticatorInterface $inner;
    private LoggerInterface $logger;
    private array $metrics = [
        'requests' => 0,
        'errors' => 0,
        'total_time' => 0,
    ];

    public function __construct(RequestAuthenticatorInterface $inner, LoggerInterface $logger)
    {
        $this->inner = $inner;
        $this->logger = $logger;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $startTime = microtime(true);
        $this->metrics['requests']++;

        $context = [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'headers' => $this->sanitizeHeaders($request->getHeaders()),
            'request_id' => uniqid('req_'),
        ];

        $this->logger->info('Début de requête API', $context);

        try {
            $authenticatedRequest = $this->inner->authenticate($request);
            
            $duration = (microtime(true) - $startTime) * 1000;
            $this->metrics['total_time'] += $duration;
            
            $this->logger->info('Authentification réussie', [
                'request_id' => $context['request_id'],
                'duration_ms' => round($duration, 2)
            ]);

            return $authenticatedRequest;

        } catch (\Exception $e) {
            $this->metrics['errors']++;
            
            $duration = (microtime(true) - $startTime) * 1000;
            $this->metrics['total_time'] += $duration;
            
            $this->logger->error('Erreur d\'authentification', [
                'request_id' => $context['request_id'],
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'duration_ms' => round($duration, 2)
            ]);

            throw $e;
        }
    }

    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;
        
        // Supprimer les en-têtes sensibles
        unset(
            $sanitized['Authorization'],
            $sanitized['Cookie'],
            $sanitized['X-API-Key'],
            $sanitized['X-Auth-Token']
        );
        
        // Masquer partiellement certains en-têtes
        if (isset($sanitized['X-User-ID'])) {
            $sanitized['X-User-ID'] = '***' . substr($sanitized['X-User-ID'], -4);
        }
        
        return $sanitized;
    }

    public function getMetrics(): array
    {
        return $this->metrics + [
            'average_time_ms' => $this->metrics['requests'] > 0 
                ? round($this->metrics['total_time'] / $this->metrics['requests'], 2)
                : 0,
            'error_rate' => $this->metrics['requests'] > 0
                ? round(($this->metrics['errors'] / $this->metrics['requests']) * 100, 2)
                : 0
        ];
    }

    public function printMetrics(): void
    {
        $metrics = $this->getMetrics();
        echo "=== Métriques des requêtes ===\n";
        echo "Total requêtes: {$metrics['requests']}\n";
        echo "Erreurs: {$metrics['errors']}\n";
        echo "Taux d'erreur: {$metrics['error_rate']}%\n";
        echo "Temps moyen: {$metrics['average_time_ms']}ms\n";
        echo "Temps total: " . round($metrics['total_time'], 2) . "ms\n";
    }
}
```

## Retry middleware personnalisé

### RetryMiddleware avec stratégies configurables

```php
class ConfigurableRetryMiddleware
{
    private int $maxRetries;
    private array $retryableStatuses;
    private array $retryStrategies;
    private LoggerInterface $logger;

    public function __construct(
        int $maxRetries = 3,
        array $retryableStatuses = [429, 500, 502, 503, 504],
        ?LoggerInterface $logger = null
    ) {
        $this->maxRetries = $maxRetries;
        $this->retryableStatuses = $retryableStatuses;
        $this->logger = $logger ?? new \Psr\Log\NullLogger();
        
        $this->retryStrategies = [
            'exponential' => [$this, 'exponentialBackoff'],
            'linear' => [$this, 'linearBackoff'],
            'fixed' => [$this, 'fixedDelay'],
            'fibonacci' => [$this, 'fibonacciBackoff'],
        ];
    }

    public function executeWithRetry(callable $operation, string $strategy = 'exponential'): mixed
    {
        $attempt = 1;
        $lastException = null;

        while ($attempt <= $this->maxRetries + 1) {
            try {
                return $operation();
                
            } catch (\Exception $e) {
                $lastException = $e;

                if ($attempt > $this->maxRetries || !$this->shouldRetry($e)) {
                    break;
                }

                $delay = $this->calculateDelay($attempt, $strategy);
                
                $this->logger->warning("Tentative {$attempt} échouée, retry dans {$delay}ms", [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                    'max_attempts' => $this->maxRetries + 1
                ]);

                usleep($delay * 1000); // Conversion ms -> µs
                $attempt++;
            }
        }

        $this->logger->error("Toutes les tentatives ont échoué", [
            'total_attempts' => $attempt - 1,
            'final_error' => $lastException->getMessage()
        ]);

        throw $lastException;
    }

    private function shouldRetry(\Exception $e): bool
    {
        // Vérifier si c'est une exception HTTP avec un statut retryable
        if (method_exists($e, 'getStatusCode')) {
            return in_array($e->getStatusCode(), $this->retryableStatuses);
        }
        
        // Retry pour les exceptions de transport/réseau
        if ($e instanceof \Psr\Http\Client\NetworkExceptionInterface) {
            return true;
        }
        
        return false;
    }

    private function calculateDelay(int $attempt, string $strategy): int
    {
        if (!isset($this->retryStrategies[$strategy])) {
            $strategy = 'exponential';
        }

        return call_user_func($this->retryStrategies[$strategy], $attempt);
    }

    private function exponentialBackoff(int $attempt): int
    {
        $baseDelay = 100;
        $delay = $baseDelay * (2 ** ($attempt - 1));
        
        // Ajouter du jitter (±25%)
        $jitter = $delay * 0.25 * (mt_rand(-100, 100) / 100);
        
        return min(10000, max(100, $delay + $jitter));
    }

    private function linearBackoff(int $attempt): int
    {
        return min(5000, 200 * $attempt);
    }

    private function fixedDelay(int $attempt): int
    {
        return 1000; // 1 seconde fixe
    }

    private function fibonacciBackoff(int $attempt): int
    {
        static $fib = [1, 1];
        
        if (!isset($fib[$attempt])) {
            for ($i = count($fib); $i <= $attempt; $i++) {
                $fib[$i] = $fib[$i-1] + $fib[$i-2];
            }
        }
        
        return min(10000, $fib[$attempt] * 100);
    }
}
```

### Utilisation du retry avec le client

```php
class ResilientApiClient
{
    private ApiClient $client;
    private ConfigurableRetryMiddleware $retryMiddleware;

    public function __construct(ApiClient $client, ConfigurableRetryMiddleware $retryMiddleware)
    {
        $this->client = $client;
        $this->retryMiddleware = $retryMiddleware;
    }

    public function read(EndpointInterface $endpoint, array $parameters = [], array $query = []): mixed
    {
        return $this->retryMiddleware->executeWithRetry(
            fn() => $this->client->read($endpoint, $parameters, $query),
            'exponential'
        );
    }

    public function create(EndpointInterface $endpoint, array $parameters = [], array $query = [], mixed $body = null): mixed
    {
        // Les opérations d'écriture utilisent généralement moins de retry
        return $this->retryMiddleware->executeWithRetry(
            fn() => $this->client->create($endpoint, $parameters, $query, $body),
            'linear'
        );
    }

    // Autres methods CRUD...
}
```

## Middleware de circuit breaker

### Protection contre les services défaillants

```php
class CircuitBreakerMiddleware implements RequestAuthenticatorInterface
{
    private RequestAuthenticatorInterface $inner;
    private string $state = 'CLOSED'; // CLOSED, OPEN, HALF_OPEN
    private int $failureCount = 0;
    private int $failureThreshold;
    private int $recoveryTimeout;
    private int $lastFailureTime = 0;
    private LoggerInterface $logger;

    public function __construct(
        RequestAuthenticatorInterface $inner,
        int $failureThreshold = 5,
        int $recoveryTimeout = 60,
        ?LoggerInterface $logger = null
    ) {
        $this->inner = $inner;
        $this->failureThreshold = $failureThreshold;
        $this->recoveryTimeout = $recoveryTimeout;
        $this->logger = $logger ?? new \Psr\Log\NullLogger();
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if ($this->state === 'OPEN') {
            if (time() - $this->lastFailureTime >= $this->recoveryTimeout) {
                $this->state = 'HALF_OPEN';
                $this->logger->info('Circuit breaker: passage à l\'état HALF_OPEN');
            } else {
                throw new \RuntimeException('Circuit breaker OUVERT - service indisponible');
            }
        }

        try {
            $result = $this->inner->authenticate($request);
            
            // Succès - reset du compteur
            if ($this->state === 'HALF_OPEN') {
                $this->state = 'CLOSED';
                $this->failureCount = 0;
                $this->logger->info('Circuit breaker: retour à l\'état CLOSED');
            }
            
            return $result;

        } catch (\Exception $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    private function handleFailure(\Exception $e): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();

        if ($this->failureCount >= $this->failureThreshold) {
            $this->state = 'OPEN';
            $this->logger->warning('Circuit breaker: ouverture du circuit', [
                'failure_count' => $this->failureCount,
                'threshold' => $this->failureThreshold,
                'last_error' => $e->getMessage()
            ]);
        }
    }

    public function getState(): array
    {
        return [
            'state' => $this->state,
            'failure_count' => $this->failureCount,
            'failure_threshold' => $this->failureThreshold,
            'last_failure_time' => $this->lastFailureTime,
            'recovery_timeout' => $this->recoveryTimeout
        ];
    }
}
```

## Combinaison de middlewares

### Stack de middlewares complet

```php
class MiddlewareStack
{
    public static function create(
        string $token,
        LoggerInterface $logger,
        CacheInterface $cache = null
    ): RequestAuthenticatorInterface {
        
        // Authenticator de base
        $authenticator = new BearerTokenAuthenticator($token);
        
        // Circuit breaker (le plus externe)
        $authenticator = new CircuitBreakerMiddleware($authenticator, 5, 60, $logger);
        
        // Retry middleware
        $retryMiddleware = new ConfigurableRetryMiddleware(3, [429, 500, 502, 503, 504], $logger);
        
        // Logging middleware
        $authenticator = new EnhancedLoggingMiddleware($authenticator, $logger);
        
        return $authenticator;
    }
}

// Utilisation
$logger = new \Monolog\Logger('api');
$authenticator = MiddlewareStack::create('votre-token', $logger);

$client = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $authenticator,
    $baseUrl,
    $contentTypeMatcher
);
```

## Tests des middlewares

### Test du LoggingMiddleware

```php
class LoggingMiddlewareTest extends TestCase
{
    public function testLogRequestAndResponse(): void
    {
        $innerAuth = $this->createMock(RequestAuthenticatorInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $request = $this->createMock(RequestInterface::class);
        $authenticatedRequest = $this->createMock(RequestInterface::class);
        
        $request->method('getMethod')->willReturn('GET');
        $request->method('getUri')->willReturn(new Uri('https://api.example.com/test'));
        $request->method('getHeaders')->willReturn([
            'Content-Type' => ['application/json'],
            'Authorization' => ['Bearer secret-token']
        ]);

        $innerAuth->expects($this->once())
                  ->method('authenticate')
                  ->with($request)
                  ->willReturn($authenticatedRequest);

        // Vérifier que les logs sont appelés avec les bonnes données
        $logger->expects($this->once())
               ->method('info')
               ->with(
                   $this->stringContains('Requête API'),
                   $this->callback(function ($context) {
                       // Vérifier que l'Authorization header est sanitisé
                       return !isset($context['headers']['Authorization']);
                   })
               );

        $middleware = new LoggingMiddleware($innerAuth, $logger);
        $result = $middleware->authenticate($request);

        $this->assertSame($authenticatedRequest, $result);
    }
}
```

## Configuration avec Symfony DI

### Services.yaml complet

```yaml
services:
    # Logger spécialisé pour l'API
    api.logger:
        class: Monolog\Logger
        arguments:
            - 'sulu-api'
        calls:
            - [pushHandler, ['@api.log_handler']]

    api.log_handler:
        class: Monolog\Handler\StreamHandler
        arguments:
            - '%kernel.logs_dir%/sulu-api.log'
            - 100 # DEBUG level

    # Authenticator de base
    api.base_authenticator:
        class: Sulu\ApiClient\Auth\BearerTokenAuthenticator
        arguments:
            $token: '%env(SULU_API_TOKEN)%'

    # Circuit breaker
    api.circuit_breaker:
        class: App\Middleware\CircuitBreakerMiddleware
        arguments:
            $inner: '@api.base_authenticator'
            $failureThreshold: 5
            $recoveryTimeout: 60
            $logger: '@api.logger'

    # Logging middleware
    api.logging_middleware:
        class: Sulu\ApiClient\Middleware\LoggingMiddleware
        arguments:
            $inner: '@api.circuit_breaker'
            $logger: '@api.logger'

    # Retry middleware
    api.retry_middleware:
        class: App\Middleware\ConfigurableRetryMiddleware
        arguments:
            $maxRetries: 3
            $retryableStatuses: [429, 500, 502, 503, 504]
            $logger: '@api.logger'

    # Client final
    Sulu\ApiClient\ApiClient:
        arguments:
            $authenticator: '@api.logging_middleware'
            # ... autres arguments
```

## Monitoring et observabilité

### Métriques Prometheus

```php
class PrometheusMetricsMiddleware implements RequestAuthenticatorInterface
{
    private RequestAuthenticatorInterface $inner;
    private \Prometheus\CollectorRegistry $registry;
    private \Prometheus\Counter $requestCounter;
    private \Prometheus\Histogram $requestDuration;
    private \Prometheus\Counter $errorCounter;

    public function __construct(RequestAuthenticatorInterface $inner, \Prometheus\CollectorRegistry $registry)
    {
        $this->inner = $inner;
        $this->registry = $registry;

        $this->requestCounter = $registry->getOrRegisterCounter(
            'sulu_api',
            'requests_total',
            'Total API requests',
            ['method', 'endpoint']
        );

        $this->requestDuration = $registry->getOrRegisterHistogram(
            'sulu_api',
            'request_duration_seconds',
            'Request duration',
            ['method', 'endpoint']
        );

        $this->errorCounter = $registry->getOrRegisterCounter(
            'sulu_api',
            'errors_total',
            'Total API errors',
            ['method', 'endpoint', 'error_type']
        );
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $startTime = microtime(true);
        $method = $request->getMethod();
        $endpoint = $this->extractEndpoint($request->getUri());

        $this->requestCounter->inc([$method, $endpoint]);

        try {
            $result = $this->inner->authenticate($request);
            
            $duration = microtime(true) - $startTime;
            $this->requestDuration->observe($duration, [$method, $endpoint]);
            
            return $result;

        } catch (\Exception $e) {
            $this->errorCounter->inc([$method, $endpoint, get_class($e)]);
            
            $duration = microtime(true) - $startTime;
            $this->requestDuration->observe($duration, [$method, $endpoint]);
            
            throw $e;
        }
    }

    private function extractEndpoint(\Psr\Http\Message\UriInterface $uri): string
    {
        $path = $uri->getPath();
        // Normaliser les chemins avec IDs: /contacts/123 -> /contacts/{id}
        return preg_replace('/\/\d+/', '/{id}', $path);
    }
}
```

Continuez avec la [pagination](07-pagination.md) pour maîtriser la gestion des grandes collections de données.