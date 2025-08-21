# Endpoints

## Architecture des endpoints

Les endpoints dans le client API Sulu suivent une architecture bien définie basée sur l'interface `EndpointInterface` et la classe abstraite `AbstractEndpoint`.

### Structure de base

```php
use Sulu\ApiClient\Endpoint\EndpointInterface;

interface EndpointInterface
{
    /**
     * Construire, authentifier et envoyer la requête HTTP
     */
    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface;

    /**
     * Parser et mapper la réponse brute en données métier
     */
    public function parseResponse(ResponseInterface $response): mixed;
}
```

## Endpoints Sulu pré-construits

Le client fournit plus de 90 endpoints pré-construits pour l'API Sulu, organisés par entité et opération :

### Nomenclature des endpoints

Les endpoints suivent une nomenclature standardisée :
- `SuluGet{Entity}Endpoint` - Récupération d'une entité unique
- `SuluGet{Entities}Endpoint` - Récupération d'une collection
- `SuluPost{Entity}Endpoint` - Création d'une entité
- `SuluPut{Entity}Endpoint` - Mise à jour d'une entité
- `SuluDelete{Entity}Endpoint` - Suppression d'une entité

### Exemples d'endpoints disponibles

#### Contacts
```php
use Sulu\ApiClient\Endpoint\SuluGetContactEndpoint;
use Sulu\ApiClient\Endpoint\SuluGetContactsEndpoint;
use Sulu\ApiClient\Endpoint\SuluPostContactEndpoint;
use Sulu\ApiClient\Endpoint\SuluPutContactEndpoint;
use Sulu\ApiClient\Endpoint\SuluDeleteContactEndpoint;
```

#### Comptes (Accounts)
```php
use Sulu\ApiClient\Endpoint\SuluGetAccountEndpoint;
use Sulu\ApiClient\Endpoint\SuluGetAccountsEndpoint;
use Sulu\ApiClient\Endpoint\SuluPostAccountEndpoint;
use Sulu\ApiClient\Endpoint\SuluPutAccountEndpoint;
use Sulu\ApiClient\Endpoint\SuluDeleteAccountEndpoint;
```

#### Pages
```php
use Sulu\ApiClient\Endpoint\SuluGetPageEndpoint;
use Sulu\ApiClient\Endpoint\SuluGetPagesEndpoint;
use Sulu\ApiClient\Endpoint\SuluPostPageEndpoint;
use Sulu\ApiClient\Endpoint\SuluPutPageEndpoint;
use Sulu\ApiClient\Endpoint\SuluDeletePageEndpoint;
```

#### Articles
```php
use Sulu\ApiClient\Endpoint\SuluGetArticleEndpoint;
use Sulu\ApiClient\Endpoint\SuluGetArticlesEndpoint;
use Sulu\ApiClient\Endpoint\SuluPostArticleEndpoint;
use Sulu\ApiClient\Endpoint\SuluPutArticleEndpoint;
use Sulu\ApiClient\Endpoint\SuluDeleteArticleEndpoint;
```

## Création d'endpoints personnalisés

### Endpoint simple

Pour créer un endpoint personnalisé, héritez de `AbstractEndpoint` :

```php
<?php

namespace App\ApiClient\Endpoint;

use Sulu\ApiClient\Endpoint\AbstractEndpoint;
use Psr\Http\Message\ResponseInterface;

final class CustomGetUserEndpoint extends AbstractEndpoint
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUriTemplate(): string
    {
        return '/api/users/{id}';
    }

    public function getDefaultContentType(): string
    {
        return 'application/json';
    }

    public function handleSuccessResponse(int $status, mixed $data, string $body): mixed
    {
        // Transformation personnalisée des données
        if (is_array($data) && isset($data['user'])) {
            return $data['user'];
        }

        return $data;
    }
}
```

### Endpoint avec validation personnalisée

```php
final class CustomPostUserEndpoint extends AbstractEndpoint
{
    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUriTemplate(): string
    {
        return '/api/users';
    }

    public function getDefaultContentType(): string
    {
        return 'application/json';
    }

    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        // Validation personnalisée avant envoi
        if ($body !== null && is_array($body)) {
            $this->validateUserData($body);
        }

        return parent::request($parameters, $query, $body);
    }

    private function validateUserData(array $data): void
    {
        if (empty($data['email'])) {
            throw new \InvalidArgumentException('L\'email est requis');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Format d\'email invalide');
        }

        if (empty($data['username']) || strlen($data['username']) < 3) {
            throw new \InvalidArgumentException('Le nom d\'utilisateur doit contenir au moins 3 caractères');
        }
    }

    public function handleSuccessResponse(int $status, mixed $data, string $body): mixed
    {
        // Traitement personnalisé de la réponse de création
        if ($status === 201 && is_array($data)) {
            return [
                'id' => $data['id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'created_at' => $data['createdAt'] ?? date('Y-m-d H:i:s')
            ];
        }

        return $data;
    }
}
```

### Endpoint avec authentification spécifique

```php
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use Psr\Http\Message\RequestInterface;

final class AdminOnlyEndpoint extends AbstractEndpoint
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUriTemplate(): string
    {
        return '/api/admin/settings';
    }

    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        // Ajouter des en-têtes spécifiques pour l'admin
        $request = $this->requestFactory->createRequest($this->getMethod(), $this->buildUri($parameters, $query));
        
        // Ajouter l'authentification
        $request = $this->authenticator->authenticate($request);
        
        // Ajouter des en-têtes admin spécifiques
        $request = $request->withHeader('X-Admin-Access', 'required');
        $request = $request->withHeader('X-Permission-Level', 'admin');

        if ($body !== null) {
            $payload = $this->serializer->serialize($body, 'json');
            $request = $request->withBody($this->requestFactory->createStream($payload))
                              ->withHeader('Content-Type', $this->getDefaultContentType());
        }

        return $this->http->sendRequest($request);
    }
}
```

## Gestion avancée des endpoints

### Content-Type dynamique

```php
final class FlexibleContentTypeEndpoint extends AbstractEndpoint
{
    private string $contentType = 'application/json';

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getDefaultContentType(): string
    {
        return $this->contentType;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUriTemplate(): string
    {
        return '/api/flexible-upload';
    }

    public function handleSuccessResponse(int $status, mixed $data, string $body): mixed
    {
        // Traitement différent selon le content-type
        return match ($this->contentType) {
            'application/xml' => $this->parseXmlResponse($body),
            'text/plain' => $body,
            default => $data
        };
    }

    private function parseXmlResponse(string $body): array
    {
        return json_decode(json_encode(simplexml_load_string($body)), true);
    }
}
```

### Endpoint avec gestion de fichiers

```php
final class FileUploadEndpoint extends AbstractEndpoint
{
    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUriTemplate(): string
    {
        return '/api/files';
    }

    public function getDefaultContentType(): string
    {
        return 'multipart/form-data';
    }

    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($this->getMethod(), $this->buildUri($parameters, $query));
        $request = $this->authenticator->authenticate($request);

        if ($body !== null && is_array($body)) {
            // Gérer l'upload de fichiers
            $boundary = uniqid();
            $multipartBody = $this->buildMultipartBody($body, $boundary);
            
            $request = $request->withBody($this->requestFactory->createStream($multipartBody))
                              ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
        }

        return $this->http->sendRequest($request);
    }

    private function buildMultipartBody(array $data, string $boundary): string
    {
        $body = '';
        
        foreach ($data as $key => $value) {
            $body .= "--{$boundary}\r\n";
            
            if ($value instanceof \SplFileInfo) {
                $body .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"" . $value->getFilename() . "\"\r\n";
                $body .= "Content-Type: " . mime_content_type($value->getPathname()) . "\r\n\r\n";
                $body .= file_get_contents($value->getPathname()) . "\r\n";
            } else {
                $body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
                $body .= $value . "\r\n";
            }
        }
        
        $body .= "--{$boundary}--\r\n";
        
        return $body;
    }
}
```

## Tests d'endpoints

### Test unitaire d'un endpoint personnalisé

```php
<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sulu\ApiClient\Serializer\SerializerInterface;
use Sulu\ApiClient\Auth\RequestAuthenticatorInterface;
use App\ApiClient\Endpoint\CustomGetUserEndpoint;

class CustomGetUserEndpointTest extends TestCase
{
    public function testGetUser(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $authenticator = $this->createMock(RequestAuthenticatorInterface::class);
        $contentTypeMatcher = $this->createMock(ContentTypeMatcherInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn('{"user":{"id":1,"name":"Jean"}}');
        
        $httpClient->method('sendRequest')->willReturn($response);
        $serializer->method('deserialize')->willReturn(['user' => ['id' => 1, 'name' => 'Jean']]);

        $endpoint = new CustomGetUserEndpoint(
            $httpClient,
            $requestFactory,
            $serializer,
            $authenticator,
            $contentTypeMatcher,
            'https://api.example.com'
        );

        $result = $endpoint->parseResponse($response);

        $this->assertEquals(['id' => 1, 'name' => 'Jean'], $result);
    }
}
```

## Bonnes pratiques pour les endpoints

### 1. Nommage cohérent

```php
// ✅ Correct
final class GetUserProfileEndpoint extends AbstractEndpoint { }
final class PostUserEndpoint extends AbstractEndpoint { }
final class PutUserEndpoint extends AbstractEndpoint { }

// ❌ Incorrect
final class UserProfileGetter extends AbstractEndpoint { }
final class CreateUser extends AbstractEndpoint { }
```

### 2. Gestion d'erreurs spécifiques

```php
public function handleSuccessResponse(int $status, mixed $data, string $body): mixed
{
    if ($status === 202) {
        // Traitement asynchrone
        return ['status' => 'processing', 'job_id' => $data['jobId'] ?? null];
    }

    return $data;
}

protected function throwHttpException(int $status, mixed $data, ResponseInterface $response): never
{
    if ($status === 402) {
        throw new PaymentRequiredException('Paiement requis pour accéder à cette ressource');
    }

    parent::throwHttpException($status, $data, $response);
}
```

### 3. Documentation des endpoints

```php
/**
 * Endpoint pour récupérer les détails d'un utilisateur.
 * 
 * @example
 * $endpoint = new GetUserEndpoint(...);
 * $user = $client->read($endpoint, ['id' => 123]);
 * 
 * @param array $parameters ['id' => int] ID de l'utilisateur
 * @param array $query Paramètres optionnels ['fields' => string, 'embed' => string]
 * @return array Données utilisateur avec les champs demandés
 * 
 * @throws NotFoundException Si l'utilisateur n'existe pas
 * @throws ForbiddenException Si l'accès est refusé
 */
final class GetUserEndpoint extends AbstractEndpoint
{
    // Implementation...
}
```

### 4. Réutilisabilité

```php
abstract class BaseUserEndpoint extends AbstractEndpoint
{
    protected function validateUserId(array $parameters): void
    {
        if (!isset($parameters['id']) || !is_numeric($parameters['id'])) {
            throw new \InvalidArgumentException('ID utilisateur requis et doit être numérique');
        }
    }

    protected function formatUserResponse(array $data): array
    {
        return [
            'id' => (int) $data['id'],
            'username' => $data['username'],
            'email' => $data['email'],
            'created_at' => $data['createdAt'],
            'updated_at' => $data['updatedAt'] ?? null
        ];
    }
}

final class GetUserEndpoint extends BaseUserEndpoint
{
    public function request(array $parameters = [], array $query = [], mixed $body = null): ResponseInterface
    {
        $this->validateUserId($parameters);
        return parent::request($parameters, $query, $body);
    }

    public function handleSuccessResponse(int $status, mixed $data, string $body): mixed
    {
        return $this->formatUserResponse($data);
    }
}
```

Continuez avec l'[authentification](04-authentification.md) pour comprendre les différents systèmes d'authentification disponibles.