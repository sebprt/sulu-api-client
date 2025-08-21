# Pagination

## Introduction à la pagination

Le client API Sulu prend en charge la pagination par curseur (cursor-based pagination), un système robuste pour parcourir de grandes collections de données. Cette approche est particulièrement efficace pour les données en constante évolution et évite les problèmes de décalage rencontrés avec la pagination par page traditionnelle.

## Architecture de la pagination

Le système de pagination utilise deux composants principaux :

- **`CursorPaginator`** : Itérateur pour parcourir automatiquement les pages
- **`CursorPage`** : Représente une page de données avec métadonnées de curseur

### Principe de fonctionnement

```php
// Structure type d'une réponse paginée Sulu
{
    "_embedded": {
        "contacts": [
            // ... données des contacts
        ]
    },
    "_links": {
        "next": {
            "href": "/api/contacts?cursor=eyJpZCI6MTIzfQ=="
        },
        "prev": {
            "href": "/api/contacts?cursor=eyJpZCI6OTl9"
        }
    },
    "page": {
        "size": 20,
        "totalElements": 1500
    }
}
```

## Utilisation de base

### Pagination simple avec foreach

```php
use Sulu\ApiClient\Endpoint\SuluGetContactsEndpoint;

$endpointFactory = $client->getEndpointFactory();
$endpoint = $endpointFactory->create(SuluGetContactsEndpoint::class);

// Création du paginateur
$paginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    'contacts',  // Clé d'intégration
    [],         // Paramètres d'URL
    [],         // Query parameters de base
    20,         // Limite par page
    null        // Curseur initial (null = début)
);

// Itération automatique sur toutes les pages
foreach ($paginator as $page) {
    echo "Page avec " . count($page->getItems()) . " éléments\n";
    
    foreach ($page->getItems() as $contact) {
        echo "- {$contact['firstName']} {$contact['lastName']}\n";
    }
    
    // Métadonnées de la page
    echo "Curseur suivant: " . ($page->getNextCursor() ?? 'aucun') . "\n";
    echo "Total estimé: " . ($page->getTotalElements() ?? 'inconnu') . "\n\n";
}
```

### Pagination manuelle avec contrôle

```php
$paginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    'contacts',
    [],
    ['limit' => 50], // Plus grande limite
    50
);

$pageNumber = 1;
$totalProcessed = 0;

while ($paginator->valid()) {
    $page = $paginator->current();
    
    echo "=== Page {$pageNumber} ===\n";
    
    // Traitement des données
    $contacts = $page->getItems();
    $totalProcessed += count($contacts);
    
    foreach ($contacts as $contact) {
        // Traitement métier
        processContact($contact);
    }
    
    echo "Traités: " . count($contacts) . " contacts\n";
    echo "Total traité: {$totalProcessed}\n";
    
    // Passer à la page suivante
    $paginator->next();
    $pageNumber++;
    
    // Pause optionnelle pour éviter de surcharger l'API
    if ($pageNumber % 10 === 0) {
        echo "Pause de 2 secondes...\n";
        sleep(2);
    }
}

echo "Pagination terminée. Total: {$totalProcessed} contacts traités.\n";
```

## Pagination avec filtres et recherche

### Recherche paginée

```php
use Sulu\ApiClient\Endpoint\SuluGetContactsEndpoint;

$endpoint = $endpointFactory->create(SuluGetContactsEndpoint::class);

// Recherche avec critères
$searchQuery = [
    'search' => 'martin',
    'fields' => 'id,firstName,lastName,email',
    'sortBy' => 'lastName',
    'sortOrder' => 'ASC'
];

$paginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    'contacts',
    [],
    $searchQuery,
    25
);

echo "Recherche pour 'martin':\n";
foreach ($paginator as $page) {
    foreach ($page->getItems() as $contact) {
        echo "- {$contact['firstName']} {$contact['lastName']} ({$contact['email']})\n";
    }
}
```

### Filtrage par critères complexes

```php
// Filtres avancés
$complexQuery = [
    'filters' => [
        'account.type' => 'customer',
        'created' => ['gte' => '2024-01-01'],
        'tags' => 'vip,premium'
    ],
    'embed' => 'account,tags',
    'limit' => 20
];

$paginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    'contacts',
    [],
    $complexQuery,
    20
);

$vipContacts = [];
foreach ($paginator as $page) {
    foreach ($page->getItems() as $contact) {
        if (isset($contact['tags']) && in_array('vip', array_column($contact['tags'], 'name'))) {
            $vipContacts[] = $contact;
        }
    }
}

echo "Trouvé " . count($vipContacts) . " contacts VIP\n";
```

## Gestion avancée de la pagination

### Reprise de pagination avec curseur

```php
class PaginationStateManager
{
    private string $stateFile;

    public function __construct(string $stateFile = 'pagination_state.json')
    {
        $this->stateFile = $stateFile;
    }

    public function saveState(string $jobId, ?string $cursor, int $processed): void
    {
        $state = $this->loadAllStates();
        $state[$jobId] = [
            'cursor' => $cursor,
            'processed' => $processed,
            'timestamp' => time()
        ];
        
        file_put_contents($this->stateFile, json_encode($state, JSON_PRETTY_PRINT));
    }

    public function loadState(string $jobId): ?array
    {
        $states = $this->loadAllStates();
        return $states[$jobId] ?? null;
    }

    public function clearState(string $jobId): void
    {
        $state = $this->loadAllStates();
        unset($state[$jobId]);
        file_put_contents($this->stateFile, json_encode($state, JSON_PRETTY_PRINT));
    }

    private function loadAllStates(): array
    {
        if (!file_exists($this->stateFile)) {
            return [];
        }
        
        return json_decode(file_get_contents($this->stateFile), true) ?? [];
    }
}

// Usage avec reprise
function processAllContacts(ApiClient $client, string $jobId = 'contact_export')
{
    $stateManager = new PaginationStateManager();
    $state = $stateManager->loadState($jobId);
    
    $initialCursor = $state['cursor'] ?? null;
    $processed = $state['processed'] ?? 0;
    
    echo "Reprise du traitement depuis le curseur: " . ($initialCursor ?? 'début') . "\n";
    echo "Déjà traités: {$processed} contacts\n";

    $endpoint = $client->getEndpointFactory()->create(SuluGetContactsEndpoint::class);
    
    $paginator = $client->paginateEmbeddedCursorCollection(
        $endpoint,
        'contacts',
        [],
        ['limit' => 100],
        100,
        $initialCursor // Reprendre depuis le curseur sauvegardé
    );

    try {
        foreach ($paginator as $page) {
            foreach ($page->getItems() as $contact) {
                // Traitement du contact
                processContact($contact);
                $processed++;
                
                // Sauvegarde périodique de l'état
                if ($processed % 50 === 0) {
                    $stateManager->saveState($jobId, $page->getNextCursor(), $processed);
                    echo "État sauvegardé - Traités: {$processed}\n";
                }
            }
        }
        
        // Nettoyage final
        $stateManager->clearState($jobId);
        echo "Traitement terminé. Total: {$processed} contacts.\n";
        
    } catch (\Exception $e) {
        // Sauvegarde d'urgence en cas d'erreur
        $stateManager->saveState($jobId, $paginator->current()->getNextCursor(), $processed);
        echo "Erreur: " . $e->getMessage() . "\n";
        echo "État sauvegardé pour reprise ultérieure.\n";
        throw $e;
    }
}
```

### Pagination parallèle

```php
class ParallelPagination
{
    private ApiClient $client;
    private int $maxWorkers;

    public function __construct(ApiClient $client, int $maxWorkers = 4)
    {
        $this->client = $client;
        $this->maxWorkers = $maxWorkers;
    }

    public function processInParallel(EndpointInterface $endpoint, string $embeddedKey, callable $processor): void
    {
        // Première passe : découvrir les curseurs pour parallélisation
        $cursors = $this->discoverCursors($endpoint, $embeddedKey);
        
        // Division en chunks pour les workers
        $chunks = array_chunk($cursors, ceil(count($cursors) / $this->maxWorkers));
        
        $processes = [];
        
        foreach ($chunks as $workerIndex => $workerCursors) {
            $pid = pcntl_fork();
            
            if ($pid === -1) {
                throw new \RuntimeException("Impossible de créer le processus worker {$workerIndex}");
            } elseif ($pid === 0) {
                // Processus enfant
                $this->workerProcess($workerIndex, $endpoint, $embeddedKey, $workerCursors, $processor);
                exit(0);
            } else {
                // Processus parent
                $processes[$workerIndex] = $pid;
            }
        }
        
        // Attendre tous les workers
        foreach ($processes as $workerIndex => $pid) {
            pcntl_waitpid($pid, $status);
            echo "Worker {$workerIndex} terminé avec le statut {$status}\n";
        }
    }

    private function discoverCursors(EndpointInterface $endpoint, string $embeddedKey): array
    {
        $cursors = [];
        $paginator = $this->client->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, [], [], 100);
        
        foreach ($paginator as $page) {
            if ($page->getNextCursor()) {
                $cursors[] = $page->getNextCursor();
            }
            
            // Limiter la découverte pour éviter de traiter toute la collection
            if (count($cursors) >= $this->maxWorkers * 10) {
                break;
            }
        }
        
        return $cursors;
    }

    private function workerProcess(int $workerIndex, EndpointInterface $endpoint, string $embeddedKey, array $cursors, callable $processor): void
    {
        echo "Worker {$workerIndex} démarré avec " . count($cursors) . " curseurs\n";
        
        foreach ($cursors as $cursor) {
            $paginator = $this->client->paginateEmbeddedCursorCollection(
                $endpoint, 
                $embeddedKey, 
                [], 
                [], 
                100, 
                $cursor
            );
            
            foreach ($paginator as $page) {
                foreach ($page->getItems() as $item) {
                    $processor($item, $workerIndex);
                }
                
                // Traiter seulement une page par curseur dans ce worker
                break;
            }
        }
        
        echo "Worker {$workerIndex} terminé\n";
    }
}
```

## Optimisations de performance

### Pagination avec mise en cache

```php
class CachedPagination
{
    private CachedApiClient $cachedClient;
    private CacheInterface $paginationCache;
    private int $cacheTtl;

    public function __construct(CachedApiClient $cachedClient, CacheInterface $paginationCache, int $cacheTtl = 300)
    {
        $this->cachedClient = $cachedClient;
        $this->paginationCache = $paginationCache;
        $this->cacheTtl = $cacheTtl;
    }

    public function getPaginatedResults(EndpointInterface $endpoint, string $embeddedKey, array $query = [], int $limit = 20): \Generator
    {
        $cacheKey = $this->generateCacheKey($endpoint, $embeddedKey, $query, $limit);
        
        // Vérifier si des résultats sont en cache
        $cachedResults = $this->paginationCache->get($cacheKey);
        if ($cachedResults !== null) {
            foreach ($cachedResults as $pageData) {
                yield new CursorPage($pageData['items'], $pageData['nextCursor'], $pageData['totalElements']);
            }
            return;
        }

        // Si pas en cache, récupérer et mettre en cache
        $allPages = [];
        $paginator = $this->cachedClient->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, [], $query, $limit);

        foreach ($paginator as $page) {
            $pageData = [
                'items' => $page->getItems(),
                'nextCursor' => $page->getNextCursor(),
                'totalElements' => $page->getTotalElements()
            ];
            
            $allPages[] = $pageData;
            yield $page;

            // Mise en cache progressive (toutes les 5 pages)
            if (count($allPages) % 5 === 0) {
                $this->paginationCache->set($cacheKey, $allPages, $this->cacheTtl);
            }
        }

        // Cache final
        $this->paginationCache->set($cacheKey, $allPages, $this->cacheTtl);
    }

    private function generateCacheKey(EndpointInterface $endpoint, string $embeddedKey, array $query, int $limit): string
    {
        $keyData = [
            'endpoint' => $endpoint::class,
            'embedded_key' => $embeddedKey,
            'query' => $query,
            'limit' => $limit
        ];

        return 'pagination:' . md5(serialize($keyData));
    }
}
```

### Pagination avec streaming

```php
class StreamingPagination
{
    private ApiClient $client;
    private LoggerInterface $logger;

    public function __construct(ApiClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function streamToFile(EndpointInterface $endpoint, string $embeddedKey, string $outputFile, array $query = []): void
    {
        $handle = fopen($outputFile, 'w');
        if (!$handle) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier {$outputFile} en écriture");
        }

        try {
            // Écrire l'en-tête JSON
            fwrite($handle, "[\n");
            
            $paginator = $this->client->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, [], $query, 100);
            
            $isFirstItem = true;
            $totalItems = 0;
            
            foreach ($paginator as $pageIndex => $page) {
                foreach ($page->getItems() as $item) {
                    if (!$isFirstItem) {
                        fwrite($handle, ",\n");
                    }
                    
                    fwrite($handle, json_encode($item, JSON_PRETTY_PRINT));
                    $isFirstItem = false;
                    $totalItems++;
                    
                    // Log périodique
                    if ($totalItems % 1000 === 0) {
                        $this->logger->info("Streamés {$totalItems} éléments vers {$outputFile}");
                    }
                }
                
                // Forcer l'écriture sur disque
                fflush($handle);
            }
            
            // Fermer le JSON
            fwrite($handle, "\n]");
            
            $this->logger->info("Streaming terminé. {$totalItems} éléments écrits dans {$outputFile}");
            
        } finally {
            fclose($handle);
        }
    }

    public function streamToCsv(EndpointInterface $endpoint, string $embeddedKey, string $csvFile, array $headers, array $query = []): void
    {
        $handle = fopen($csvFile, 'w');
        if (!$handle) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier CSV {$csvFile}");
        }

        try {
            // Écrire les en-têtes CSV
            fputcsv($handle, $headers);
            
            $paginator = $this->client->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, [], $query, 100);
            
            $totalRows = 0;
            
            foreach ($paginator as $page) {
                foreach ($page->getItems() as $item) {
                    $row = [];
                    foreach ($headers as $header) {
                        $row[] = $this->extractValue($item, $header);
                    }
                    
                    fputcsv($handle, $row);
                    $totalRows++;
                }
                
                fflush($handle);
            }
            
            $this->logger->info("Export CSV terminé. {$totalRows} lignes dans {$csvFile}");
            
        } finally {
            fclose($handle);
        }
    }

    private function extractValue(array $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $data;
        
        foreach ($keys as $key) {
            if (!is_array($current) || !isset($current[$key])) {
                return '';
            }
            $current = $current[$key];
        }
        
        return $current;
    }
}
```

## Gestion des erreurs de pagination

### Retry automatique pour la pagination

```php
class ResilientPagination
{
    private ApiClient $client;
    private int $maxRetries;
    private LoggerInterface $logger;

    public function __construct(ApiClient $client, int $maxRetries = 3, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->maxRetries = $maxRetries;
        $this->logger = $logger ?? new NullLogger();
    }

    public function paginateWithRetry(EndpointInterface $endpoint, string $embeddedKey, array $query = [], int $limit = 20): \Generator
    {
        $cursor = null;
        $pageNumber = 1;
        
        do {
            $page = $this->getPageWithRetry($endpoint, $embeddedKey, $query, $limit, $cursor, $pageNumber);
            
            if ($page === null) {
                $this->logger->warning("Impossible de récupérer la page {$pageNumber} après {$this->maxRetries} tentatives");
                break;
            }
            
            yield $page;
            
            $cursor = $page->getNextCursor();
            $pageNumber++;
            
        } while ($cursor !== null);
    }

    private function getPageWithRetry(EndpointInterface $endpoint, string $embeddedKey, array $query, int $limit, ?string $cursor, int $pageNumber): ?CursorPage
    {
        $attempt = 1;
        
        while ($attempt <= $this->maxRetries) {
            try {
                $paginator = $this->client->paginateEmbeddedCursorCollection($endpoint, $embeddedKey, [], $query, $limit, $cursor);
                
                // Récupérer la première (et seule) page de ce paginator
                foreach ($paginator as $page) {
                    return $page;
                }
                
                return null; // Pas de page trouvée
                
            } catch (\Exception $e) {
                $this->logger->warning("Tentative {$attempt}/{$this->maxRetries} échouée pour la page {$pageNumber}", [
                    'error' => $e->getMessage(),
                    'cursor' => $cursor
                ]);
                
                if ($attempt >= $this->maxRetries) {
                    $this->logger->error("Toutes les tentatives ont échoué pour la page {$pageNumber}");
                    throw $e;
                }
                
                // Délai exponentiel
                $delay = (2 ** ($attempt - 1)) * 1000000; // microsecondes
                usleep($delay);
                
                $attempt++;
            }
        }
        
        return null;
    }
}
```

## Tests de pagination

### Test du CursorPaginator

```php
class CursorPaginatorTest extends TestCase
{
    public function testPaginationFlow(): void
    {
        $client = $this->createMock(ApiClient::class);
        $endpoint = $this->createMock(EndpointInterface::class);

        // Simuler les réponses paginées
        $client->expects($this->exactly(3))
               ->method('collection')
               ->willReturnOnConsecutiveCalls(
                   // Page 1
                   [
                       '_embedded' => ['contacts' => [['id' => 1], ['id' => 2]]],
                       '_links' => ['next' => ['href' => '/api/contacts?cursor=abc123']]
                   ],
                   // Page 2
                   [
                       '_embedded' => ['contacts' => [['id' => 3], ['id' => 4]]],
                       '_links' => ['next' => ['href' => '/api/contacts?cursor=def456']]
                   ],
                   // Page 3 (dernière)
                   [
                       '_embedded' => ['contacts' => [['id' => 5]]],
                       '_links' => []
                   ]
               );

        $paginator = new CursorPaginator($client, $endpoint, 'contacts', [], [], 2);

        $allItems = [];
        $pageCount = 0;

        foreach ($paginator as $page) {
            $pageCount++;
            $allItems = array_merge($allItems, $page->getItems());
        }

        $this->assertEquals(3, $pageCount);
        $this->assertEquals(5, count($allItems));
        $this->assertEquals([1, 2, 3, 4, 5], array_column($allItems, 'id'));
    }
}
```

## Bonnes pratiques

### 1. Gestion de la mémoire

```php
// ✅ Correct - traitement élément par élément
foreach ($paginator as $page) {
    foreach ($page->getItems() as $item) {
        processItem($item);
    }
    // La mémoire est libérée après chaque page
}

// ❌ Incorrect - accumulation en mémoire
$allItems = [];
foreach ($paginator as $page) {
    $allItems = array_merge($allItems, $page->getItems());
}
```

### 2. Limitations de taux

```php
foreach ($paginator as $pageIndex => $page) {
    foreach ($page->getItems() as $item) {
        processItem($item);
    }
    
    // Pause pour respecter les limites de taux
    if ($pageIndex % 10 === 0) {
        sleep(1);
    }
}
```

### 3. Monitoring des performances

```php
$startTime = microtime(true);
$processedCount = 0;

foreach ($paginator as $pageIndex => $page) {
    foreach ($page->getItems() as $item) {
        processItem($item);
        $processedCount++;
    }
    
    // Stats périodiques
    if ($pageIndex % 10 === 0) {
        $elapsed = microtime(true) - $startTime;
        $rate = $processedCount / $elapsed;
        echo "Traités: {$processedCount}, Taux: " . round($rate, 2) . " items/sec\n";
    }
}
```

Continuez avec les [usages avancés et bonnes pratiques](08-usage-avance.md) pour maîtriser les techniques avancées du client API.