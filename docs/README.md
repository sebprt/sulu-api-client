# Documentation du Client API Sulu

Bienvenue dans la documentation complète du client API Sulu en PHP. Cette bibliothèque offre une interface moderne et robuste pour interagir avec l'API REST de Sulu CMS.

## 📚 Table des matières

### Guide de démarrage

1. **[Installation et Configuration](01-installation-et-configuration.md)**
   - Installation via Composer
   - Configuration de base et avancée
   - Variables d'environnement
   - Intégration avec Symfony

2. **[Utilisation de base](02-utilisation-de-base.md)**
   - Opérations CRUD complètes
   - Gestion des collections
   - Factory d'endpoints
   - Gestion d'erreurs
   - Patterns d'utilisation courants

### Fonctionnalités avancées

3. **[Endpoints](03-endpoints.md)**
   - Architecture des endpoints
   - 90+ endpoints Sulu pré-construits
   - Création d'endpoints personnalisés
   - Gestion avancée et bonnes pratiques

4. **[Authentification](04-authentification.md)**
   - Architecture d'authentification flexible
   - Bearer Token et Session Cookie
   - Authentification OAuth2 et API Key
   - Authentificateurs personnalisés
   - Gestion des tokens expirés

5. **[Cache](05-cache.md)**
   - CachedApiClient avec PSR-16
   - Stratégies de cache avancées
   - Cache hiérarchique et avec tags
   - Monitoring et métriques
   - Optimisations de performance

6. **[Middleware](06-middleware.md)**
   - LoggingMiddleware avec sanitisation
   - RetryMiddleware avec backoff
   - Circuit breaker pattern
   - Stack de middlewares
   - Monitoring avec Prometheus

7. **[Pagination](07-pagination.md)**
   - Pagination par curseur robuste
   - Gestion d'état et reprise
   - Pagination parallèle
   - Streaming de données
   - Optimisations mémoire

8. **[Usage avancé](08-usage-avance.md)**
   - Architecture d'application complète
   - Patterns d'intégration
   - Optimisations de performance
   - Tests d'intégration
   - Troubleshooting

## 🚀 Démarrage rapide

```bash
# Installation
composer require sebprt/sulu-api-client

# Configuration minimale
$client = new ApiClient(
    $httpClient,
    $requestFactory, 
    $serializer,
    new BearerTokenAuthenticator('votre-token'),
    'https://votre-sulu.com/api',
    $contentTypeMatcher
);

# Utilisation
$endpoint = $client->getEndpointFactory()->create(SuluGetContactEndpoint::class);
$contact = $client->read($endpoint, ['id' => 123]);
```

## 🏗️ Architecture

Le client API Sulu suit une architecture modulaire et extensible :

- **Client principal** : `ApiClient` pour les opérations de base
- **Client avec cache** : `CachedApiClient` pour les performances
- **Endpoints** : Plus de 90 endpoints Sulu pré-construits
- **Authentification** : Système flexible avec middlewares
- **Pagination** : Support natif de la pagination par curseur
- **Cache** : Intégration PSR-16 avec invalidation intelligente

## 📊 Fonctionnalités principales

### ✅ Opérations CRUD complètes
- Create, Read, Update, Upsert, Delete
- Gestion des collections et pagination
- Support des paramètres et query strings

### ✅ Authentification flexible
- Bearer Token et Session Cookie
- OAuth2 et API Key
- Authentificateurs personnalisables
- Gestion automatique des tokens

### ✅ Cache intelligent
- Mise en cache automatique des lectures
- Invalidation lors des écritures
- Stratégies de cache avancées
- Monitoring des performances

### ✅ Middleware robuste
- Logging avec sanitisation
- Retry automatique avec backoff
- Circuit breaker pattern
- Métriques et monitoring

### ✅ Pagination efficace
- Pagination par curseur
- Gestion d'état pour reprise
- Streaming pour gros volumes
- Support du traitement parallèle

## 🔧 Configuration avancée

### Avec Symfony DI

```yaml
services:
    Sulu\ApiClient\ApiClient:
        arguments:
            $authenticator: '@app.api_authenticator'
            $baseUrl: '%env(SULU_API_BASE_URL)%'
    
    Sulu\ApiClient\Cache\CachedApiClient:
        arguments:
            $client: '@Sulu\ApiClient\ApiClient'
            $cache: '@cache.app'
```

### Avec middleware complet

```php
$authenticator = MiddlewareStack::create(
    token: $_ENV['SULU_API_TOKEN'],
    logger: $logger,
    cache: $cache
);
```

## 📈 Performance

Le client est optimisé pour les performances avec :

- **Cache PSR-16** : Réduction des appels réseau
- **Pool de connexions** : Réutilisation des connexions HTTP
- **Pagination efficace** : Traitement streaming des gros volumes
- **Retry intelligent** : Gestion automatique des erreurs temporaires

## 🧪 Tests

Le client inclut une suite de tests complète :

```bash
# Tests unitaires
vendor/bin/phpunit

# Tests avec couverture
vendor/bin/phpunit --coverage-html coverage/
```

## 📖 Exemples d'usage

### Synchronisation de contacts

```php
$paginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    'contacts',
    [],
    ['modified' => ['gte' => '2024-01-01']],
    50
);

foreach ($paginator as $page) {
    foreach ($page->getItems() as $contact) {
        syncToExternalSystem($contact);
    }
}
```

### Export massif avec streaming

```php
$streaming = new StreamingPagination($client, $logger);
$streaming->streamToCsv(
    $endpoint,
    'contacts', 
    'export.csv',
    ['id', 'firstName', 'lastName', 'email']
);
```

## 🛠️ Dépannage

### Problèmes courants

1. **Erreur 429** → Utiliser RetryMiddleware
2. **Timeout** → Augmenter les délais HTTP  
3. **Mémoire** → Traiter page par page
4. **Cache** → Vérifier la configuration PSR-16

Consultez le [guide de dépannage](08-usage-avance.md#troubleshooting) pour plus de détails.

## 📝 Contribuer

Pour contribuer à ce projet :

1. Fork le repository
2. Créez une branche feature
3. Ajoutez des tests
4. Soumettez une pull request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🔗 Liens utiles

- [API Sulu Documentation](https://docs.sulu.io/)
- [PSR-16 Simple Cache](https://www.php-fig.org/psr/psr-16/)
- [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/)
- [Guzzle HTTP](https://docs.guzzlephp.org/)

---

Cette documentation est maintenue à jour avec chaque version du client API Sulu. Pour des questions spécifiques ou des rapports de bugs, ouvrez une issue sur le repository GitHub.