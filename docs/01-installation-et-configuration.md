# Installation et Configuration

## Installation via Composer

Pour installer le client API Sulu, utilisez Composer :

```bash
composer require sebprt/sulu-api-client
```

### Dépendances requises

Le client nécessite les dépendances suivantes :
- PHP ^8.2
- ext-json
- ext-curl
- ext-mbstring
- Implémentations PSR-18 (HTTP Client), PSR-17 (HTTP Factory), PSR-16 (Simple Cache)

Ces dépendances sont automatiquement installées :
- `psr/http-client` ^1.0
- `psr/http-factory` ^1.0
- `psr/http-message` ^2.0
- `psr/log` ^3.0
- `psr/simple-cache` ^3.0
- `php-http/discovery` ^1.20
- `php-http/guzzle7-adapter` ^1.1

## Configuration de base

### Création d'un client simple

```php
<?php

use Sulu\ApiClient\ApiClient;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;
use Sulu\ApiClient\Serializer\JsonSerializer;
use Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher;

// Découverte automatique des implémentations PSR
$httpClient = \Http\Discovery\Psr18ClientDiscovery::find();
$requestFactory = \Http\Discovery\Psr17FactoryDiscovery::findRequestFactory();

// Configuration de l'authentification
$authenticator = new BearerTokenAuthenticator('votre-token-api');

// Sérialiseur JSON
$serializer = new JsonSerializer();

// Matcher de type de contenu
$contentTypeMatcher = new DefaultContentTypeMatcher();

// URL de base de votre instance Sulu
$baseUrl = 'https://votre-sulu-instance.com/api';

// Création du client
$client = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $authenticator,
    $baseUrl,
    $contentTypeMatcher
);
```

### Configuration avec authentification par session

Si vous utilisez l'authentification par session/cookie :

```php
use Sulu\ApiClient\Auth\SessionCookieAuthenticator;

$authenticator = new SessionCookieAuthenticator([
    'PHPSESSID' => 'votre-session-id',
    // Autres cookies nécessaires
]);
```

## Variables d'environnement

Il est recommandé d'utiliser des variables d'environnement pour stocker les informations sensibles :

```php
// .env
SULU_API_BASE_URL=https://votre-sulu-instance.com/api
SULU_API_TOKEN=votre-token-secret

// Configuration
$baseUrl = $_ENV['SULU_API_BASE_URL'];
$authenticator = new BearerTokenAuthenticator($_ENV['SULU_API_TOKEN']);
```

## Vérification de l'installation

Pour vérifier que votre installation fonctionne correctement :

```php
// Test simple avec un endpoint existant
use Sulu\ApiClient\Endpoint\SuluGetActivitiesEndpoint;

try {
    $endpoint = new SuluGetActivitiesEndpoint(
        $httpClient,
        $requestFactory,
        $serializer,
        $authenticator,
        $contentTypeMatcher,
        $baseUrl
    );
    
    $response = $client->read($endpoint);
    echo "Connexion réussie !";
} catch (\Exception $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
```

## Configuration avancée

### Avec injection de dépendances (Symfony)

```yaml
# config/services.yaml
services:
    Sulu\ApiClient\ApiClient:
        arguments:
            $http: '@Psr\Http\Client\ClientInterface'
            $requestFactory: '@Psr\Http\Message\RequestFactoryInterface'
            $serializer: '@Sulu\ApiClient\Serializer\SerializerInterface'
            $authenticator: '@Sulu\ApiClient\Auth\RequestAuthenticatorInterface'
            $baseUrl: '%env(SULU_API_BASE_URL)%'
            $contentTypeMatcher: '@Sulu\ApiClient\Endpoint\Helper\ContentTypeMatcherInterface'

    Sulu\ApiClient\Auth\BearerTokenAuthenticator:
        arguments:
            $token: '%env(SULU_API_TOKEN)%'

    Sulu\ApiClient\Serializer\JsonSerializer: ~
    Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher: ~
```

### Configuration personnalisée du client HTTP

Si vous souhaitez configurer des options spécifiques pour le client HTTP :

```php
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as Guzzle7Adapter;

$guzzleClient = new GuzzleClient([
    'timeout' => 30,
    'verify' => true,
    'headers' => [
        'User-Agent' => 'MonApp/1.0'
    ]
]);

$httpClient = new Guzzle7Adapter($guzzleClient);
```

Maintenant que votre client est configuré, vous pouvez passer à l'[utilisation de base](02-utilisation-de-base.md).