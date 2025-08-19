# Sulu API Client (OpenAPI-synchronized)

This package provides a PSR-compliant API client with optional generation/synchronization from an OpenAPI contract.

Highlights:
- PHP >= 8.2, PSR-1/4/12, PSR-18
- Symfony HttpClient or any PSR-18 client
- Symfony Serializer (placeholder JSON serializer provided)
- Typed endpoints and DTOs (skeletons), authenticators (Bearer)
- Pagination abstractions
- Strict sync script to detect obsolete endpoints

## Installation

```
composer require sebprt/sulu-api-client
```

## Usage

```php
use Sulu\ApiClient\ApiClient;
use Sulu\ApiClient\Auth\BearerTokenAuthenticator;
use Sulu\ApiClient\Serializer\JsonSerializer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

$psr17 = new Psr17Factory();
$http = new Psr18Client();
$serializer = new JsonSerializer();
$auth = new BearerTokenAuthenticator('token');
$client = new ApiClient($http, $psr17, $serializer, $auth, 'https://localhost:8000');


// Or use a session cookie instead of a bearer token:
use Sulu\ApiClient\Auth\SessionCookieAuthenticator;
$sessionAuth = new SessionCookieAuthenticator('PHPSESSID', 'your-session-id');
$clientWithSession = new ApiClient($http, $psr17, $serializer, $sessionAuth, 'https://localhost:8000');
```

Using newly generated endpoints directly (factory + execute):

```php
use Sulu\ApiClient\Endpoint\SuluGetTagsEndpoint;

// Create the endpoint instance with client wiring
$endpoint = $client->endpoint(SuluGetTagsEndpoint::class);

// Execute it: request + parseResponse in one call
$data = $client->executeEndpoint(
    $endpoint,
    parameters: ['_format' => 'json'],
    query: ['page' => 1, 'limit' => 50]
);

// $data now contains the decoded JSON array
```

If you prefer manual control, you can also call request() and parseResponse() yourself:

```php
$response = $endpoint->request(parameters: ['_format' => 'json'], query: ['page' => 1]);
$result = $endpoint->parseResponse($response);
```

Pagination with endpoint-based helper:

```php
use Sulu\ApiClient\Endpoint\SuluGetTagsEndpoint;

$endpoint = $client->endpoint(SuluGetTagsEndpoint::class);
$paginator = $client->paginateEndpoint(
    $endpoint,
    embeddedKey: 'tags',
    parameters: ['_format' => 'json'],
    baseQuery: [],
    limit: 50,
);

foreach ($paginator as $tag) {
    // process each tag across all pages
}
```



## Generation & Synchronisation

- Source OpenAPI: docs/openapi.yaml
- Generated endpoints: src/Endpoint

Note: Synchronisation is handled outside of this package’s versioned artifacts. Refer to your build pipeline or internal tooling for how generation/sync is executed.

## Contribution

- PHPStan level max
- Rector config for PHP 8.2
- PSR-12 coding standard

## Versioning

SemVer; changes to the public API are documented in CHANGELOG.md.
