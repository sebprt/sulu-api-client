# Authentification

## Architecture d'authentification

Le client API Sulu utilise une architecture d'authentification flexible basée sur l'interface `RequestAuthenticatorInterface`. Cette approche permet de supporter différents mécanismes d'authentification et de les changer facilement.

### Interface RequestAuthenticatorInterface

```php
interface RequestAuthenticatorInterface
{
    /**
     * Applique l'authentification à une requête PSR-7 sortante.
     */
    public function authenticate(RequestInterface $request): RequestInterface;
}
```

## Types d'authentification disponibles

### 1. Authentification par token Bearer

L'authentification par token Bearer est la plus courante pour les APIs REST modernes :

```php
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;

// Création avec un token statique
$authenticator = new BearerTokenAuthenticator('votre-token-secret');

// Utilisation
$client = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $authenticator,
    $baseUrl,
    $contentTypeMatcher
);
```

**En-tête généré :**
```
Authorization: Bearer votre-token-secret
```

### 2. Authentification par cookies de session

Pour les applications utilisant l'authentification par session :

```php
use Sulu\ApiClient\Auth\SessionCookieAuthenticator;

// Avec un seul cookie
$authenticator = new SessionCookieAuthenticator([
    'PHPSESSID' => 'abc123def456'
]);

// Avec plusieurs cookies
$authenticator = new SessionCookieAuthenticator([
    'PHPSESSID' => 'abc123def456',
    'XSRF-TOKEN' => 'xyz789',
    'remember_me' => 'user123'
]);
```

**En-tête généré :**
```
Cookie: PHPSESSID=abc123def456; XSRF-TOKEN=xyz789; remember_me=user123
```

## Authentification dynamique

### Token avec renouvellement automatique

```php
class RefreshableTokenAuthenticator implements RequestAuthenticatorInterface
{
    private string $token;
    private string $refreshToken;
    private \DateTimeInterface $expiresAt;
    private HttpClientInterface $httpClient;
    private string $refreshUrl;

    public function __construct(
        string $token,
        string $refreshToken,
        \DateTimeInterface $expiresAt,
        HttpClientInterface $httpClient,
        string $refreshUrl
    ) {
        $this->token = $token;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
        $this->httpClient = $httpClient;
        $this->refreshUrl = $refreshUrl;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        // Vérifier si le token a expiré
        if (new \DateTime() >= $this->expiresAt) {
            $this->refreshAccessToken();
        }

        return $request->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    private function refreshAccessToken(): void
    {
        $refreshRequest = (new RequestFactory())->createRequest('POST', $this->refreshUrl)
            ->withHeader('Content-Type', 'application/json');

        $body = json_encode(['refresh_token' => $this->refreshToken]);
        $refreshRequest = $refreshRequest->withBody(
            (new StreamFactory())->createStream($body)
        );

        $response = $this->httpClient->sendRequest($refreshRequest);
        
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Impossible de renouveler le token');
        }

        $data = json_decode($response->getBody()->getContents(), true);
        
        $this->token = $data['access_token'];
        $this->refreshToken = $data['refresh_token'] ?? $this->refreshToken;
        $this->expiresAt = new \DateTime('+' . $data['expires_in'] . ' seconds');
    }
}
```

### Authentification OAuth 2.0

```php
class OAuth2Authenticator implements RequestAuthenticatorInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $accessToken;
    private string $tokenUrl;
    private HttpClientInterface $httpClient;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $tokenUrl,
        HttpClientInterface $httpClient
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenUrl = $tokenUrl;
        $this->httpClient = $httpClient;
        $this->obtainAccessToken();
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Bearer ' . $this->accessToken);
    }

    private function obtainAccessToken(): void
    {
        $request = (new RequestFactory())->createRequest('POST', $this->tokenUrl)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        $body = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]);

        $request = $request->withBody(
            (new StreamFactory())->createStream($body)
        );

        $response = $this->httpClient->sendRequest($request);
        
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Impossible d\'obtenir le token OAuth2');
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $data['access_token'];
    }
}
```

## Authentification avec clé API

```php
class ApiKeyAuthenticator implements RequestAuthenticatorInterface
{
    private string $apiKey;
    private string $headerName;

    public function __construct(string $apiKey, string $headerName = 'X-API-Key')
    {
        $this->apiKey = $apiKey;
        $this->headerName = $headerName;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader($this->headerName, $this->apiKey);
    }
}

// Utilisation
$authenticator = new ApiKeyAuthenticator('votre-cle-api-secrete');
$client = new ApiClient(/* ... */, $authenticator, /* ... */);
```

## Authentification basique HTTP

```php
class BasicAuthAuthenticator implements RequestAuthenticatorInterface
{
    private string $credentials;

    public function __construct(string $username, string $password)
    {
        $this->credentials = base64_encode($username . ':' . $password);
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Basic ' . $this->credentials);
    }
}

// Utilisation
$authenticator = new BasicAuthAuthenticator('utilisateur', 'motdepasse');
```

## Authentification personnalisée avec signature

```php
class SignatureAuthenticator implements RequestAuthenticatorInterface
{
    private string $accessKey;
    private string $secretKey;

    public function __construct(string $accessKey, string $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $timestamp = time();
        $nonce = uniqid();
        
        // Créer la chaîne à signer
        $stringToSign = implode("\n", [
            $request->getMethod(),
            $request->getUri()->getPath(),
            $timestamp,
            $nonce,
            md5($request->getBody()->getContents())
        ]);

        // Générer la signature HMAC
        $signature = hash_hmac('sha256', $stringToSign, $this->secretKey);

        return $request
            ->withHeader('X-Auth-AccessKey', $this->accessKey)
            ->withHeader('X-Auth-Timestamp', (string) $timestamp)
            ->withHeader('X-Auth-Nonce', $nonce)
            ->withHeader('X-Auth-Signature', $signature);
    }
}
```

## Gestion des erreurs d'authentification

### Authentificateur avec retry automatique

```php
class RetryableAuthenticator implements RequestAuthenticatorInterface
{
    private RequestAuthenticatorInterface $inner;
    private int $maxRetries;
    private int $retryCount = 0;

    public function __construct(RequestAuthenticatorInterface $inner, int $maxRetries = 3)
    {
        $this->inner = $inner;
        $this->maxRetries = $maxRetries;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        try {
            return $this->inner->authenticate($request);
        } catch (\Exception $e) {
            if ($this->retryCount < $this->maxRetries) {
                $this->retryCount++;
                sleep(pow(2, $this->retryCount)); // Backoff exponentiel
                return $this->authenticate($request);
            }
            throw $e;
        }
    }
}

// Utilisation
$baseAuth = new BearerTokenAuthenticator('token');
$retryableAuth = new RetryableAuthenticator($baseAuth, 3);
```

### Authentificateur avec fallback

```php
class FallbackAuthenticator implements RequestAuthenticatorInterface
{
    private array $authenticators;

    public function __construct(RequestAuthenticatorInterface ...$authenticators)
    {
        $this->authenticators = $authenticators;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $lastException = null;

        foreach ($this->authenticators as $authenticator) {
            try {
                return $authenticator->authenticate($request);
            } catch (\Exception $e) {
                $lastException = $e;
                continue;
            }
        }

        throw new \RuntimeException(
            'Tous les authentificateurs ont échoué',
            0,
            $lastException
        );
    }
}

// Utilisation
$primaryAuth = new BearerTokenAuthenticator('primary-token');
$backupAuth = new ApiKeyAuthenticator('backup-api-key');
$fallbackAuth = new FallbackAuthenticator($primaryAuth, $backupAuth);
```

## Tests d'authentification

### Test d'un authentificateur personnalisé

```php
<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ApiKeyAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $apiKey = 'test-api-key-123';
        $authenticator = new ApiKeyAuthenticator($apiKey);

        $request = $this->createMock(RequestInterface::class);
        $authenticatedRequest = $this->createMock(RequestInterface::class);

        $request->expects($this->once())
                ->method('withHeader')
                ->with('X-API-Key', $apiKey)
                ->willReturn($authenticatedRequest);

        $result = $authenticator->authenticate($request);

        $this->assertSame($authenticatedRequest, $result);
    }

    public function testCustomHeaderName(): void
    {
        $apiKey = 'test-api-key-123';
        $headerName = 'Custom-API-Key';
        $authenticator = new ApiKeyAuthenticator($apiKey, $headerName);

        $request = $this->createMock(RequestInterface::class);
        $authenticatedRequest = $this->createMock(RequestInterface::class);

        $request->expects($this->once())
                ->method('withHeader')
                ->with($headerName, $apiKey)
                ->willReturn($authenticatedRequest);

        $result = $authenticator->authenticate($request);

        $this->assertSame($authenticatedRequest, $result);
    }
}
```

## Bonnes pratiques pour l'authentification

### 1. Sécurité des credentials

```php
// ✅ Utiliser des variables d'environnement
$token = $_ENV['SULU_API_TOKEN'] ?? throw new \RuntimeException('Token API manquant');
$authenticator = new BearerTokenAuthenticator($token);

// ❌ Éviter les credentials en dur
$authenticator = new BearerTokenAuthenticator('mon-token-secret-123');
```

### 2. Validation des credentials

```php
class ValidatedBearerTokenAuthenticator implements RequestAuthenticatorInterface
{
    private string $token;

    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Le token ne peut pas être vide');
        }

        if (strlen($token) < 32) {
            throw new \InvalidArgumentException('Le token doit contenir au moins 32 caractères');
        }

        $this->token = $token;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('Authorization', 'Bearer ' . $this->token);
    }
}
```

### 3. Logging des authentifications

```php
class LoggingAuthenticator implements RequestAuthenticatorInterface
{
    private RequestAuthenticatorInterface $inner;
    private LoggerInterface $logger;

    public function __construct(RequestAuthenticatorInterface $inner, LoggerInterface $logger)
    {
        $this->inner = $inner;
        $this->logger = $logger;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $this->logger->debug('Authentification de la requête', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
        ]);

        try {
            $authenticatedRequest = $this->inner->authenticate($request);
            $this->logger->debug('Authentification réussie');
            return $authenticatedRequest;
        } catch (\Exception $e) {
            $this->logger->error('Échec de l\'authentification', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

### 4. Configuration avec Symfony DI

```yaml
# config/services.yaml
services:
    # Authentificateur principal
    app.api_authenticator:
        class: Sulu\ApiClient\Auth\BearerTokenAuthenticator
        arguments:
            $token: '%env(SULU_API_TOKEN)%'

    # Avec logging
    app.api_authenticator_with_logging:
        class: App\Auth\LoggingAuthenticator
        arguments:
            $inner: '@app.api_authenticator'
            $logger: '@logger'
        tags:
            - { name: monolog.logger, channel: api }

    # Client API avec authentificateur
    Sulu\ApiClient\ApiClient:
        arguments:
            $authenticator: '@app.api_authenticator_with_logging'
            # ... autres arguments
```

### 5. Gestion des tokens expirés

```php
class ExpirationAwareAuthenticator implements RequestAuthenticatorInterface
{
    private string $token;
    private ?\DateTimeInterface $expiresAt;

    public function __construct(string $token, ?\DateTimeInterface $expiresAt = null)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if ($this->expiresAt && new \DateTime() >= $this->expiresAt) {
            throw new \RuntimeException('Token expiré depuis ' . $this->expiresAt->format('Y-m-d H:i:s'));
        }

        return $request->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    public function isExpired(): bool
    {
        return $this->expiresAt && new \DateTime() >= $this->expiresAt;
    }

    public function getExpirationTime(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }
}
```

## Intégration avec le middleware

L'authentification peut être combinée avec le middleware pour des fonctionnalités avancées :

```php
use Sulu\ApiClient\Middleware\LoggingMiddleware;

// L'authentificateur avec logging intégré
$authenticator = new BearerTokenAuthenticator($token);
$loggingAuthenticator = new LoggingMiddleware($authenticator, $logger);

$client = new ApiClient(
    $httpClient,
    $requestFactory,
    $serializer,
    $loggingAuthenticator, // Middleware qui encapsule l'authentification
    $baseUrl,
    $contentTypeMatcher
);
```

Continuez avec le [cache](05-cache.md) pour améliorer les performances de vos requêtes API.