# Sulu API Client

This package provides an API client for interacting with the Sulu API.

Highlights:
- PHP >= 8.2
- Works with Symfony HttpClient and most PHP HTTP clients
- Symfony Serializer (placeholder JSON serializer provided)
- Typed endpoints and DTOs, authenticators (Bearer)
- Pagination features

## Installation

```
composer require sebprt/sulu-api-client
```

This library uses standard interfaces for HTTP messages and clients, and php-http/discovery to find installed packages. For a ready-to-run setup, consider installing:
- nyholm/psr7 and symfony/http-client

Alternatively, you can inject your own RequestFactory and HttpClient instances.

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

CRUD-style helpers for clarity (CURUD):

```php
use Sulu\ApiClient\Endpoint\SuluGetTagsEndpoint;
use Sulu\ApiClient\Endpoint\SuluDeleteCategoryEndpoint;

$endpoint = $client->createEndpoint(SuluGetTagsEndpoint::class);

// Read (GET)
$tags = $client->read($endpoint, parameters: ['_format' => 'json'], query: ['page' => 1, 'limit' => 50]);

// List (GET collection)
// Single page (no pagination handling):
$tagsList = $client->list($endpoint, parameters: ['_format' => 'json'], query: ['page' => 1, 'limit' => 50]);

// Or handle pagination directly via list(): provide the embedded collection key to get a cursor paginator
$cursor = $client->list($endpoint, parameters: ['_format' => 'json'], query: [], embeddedKey: 'tags', limit: 50);
foreach ($cursor as $tag) {
    // iterate across all items using cursor-based pagination
}

// Create (POST) — use an endpoint that performs creation
//$createEndpoint = $client->createEndpoint(SuluCreateTagEndpoint::class);
//$created = $client->create($createEndpoint, parameters: ['_format' => 'json'], body: ['name' => 'New Tag']);

// Update (PATCH) — use an endpoint that performs partial updates
//$updateEndpoint = $client->createEndpoint(SuluUpdateTagEndpoint::class);
//$updated = $client->update($updateEndpoint, parameters: ['id' => '123', '_format' => 'json'], body: ['name' => 'Renamed']);

// Upsert/Replace (PUT) — if your API supports it
//$putEndpoint = $client->createEndpoint(SuluPutTagEndpoint::class);
//$replaced = $client->upsert($putEndpoint, parameters: ['id' => '123', '_format' => 'json'], body: [/* ... */]);

// Delete (DELETE)
//$deleteEndpoint = $client->createEndpoint(SuluDeleteCategoryEndpoint::class);
//$client->delete($deleteEndpoint, parameters: ['id' => '42', '_format' => 'json']);
```

If you prefer manual control, you can also call request() and parseResponse() yourself:

```php
$response = $endpoint->request(parameters: ['_format' => 'json'], query: ['page' => 1]);
$result = $endpoint->parseResponse($response);
```

Pagination with endpoint-based helper (cursor-based):

```php
use Sulu\ApiClient\Endpoint\SuluGetTagsEndpoint;

$endpoint = $client->createEndpoint(SuluGetTagsEndpoint::class);
$cursorPaginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    embeddedKey: 'tags',
    parameters: ['_format' => 'json'],
    baseQuery: [],
    limit: 50,
);

foreach ($cursorPaginator as $tag) {
    // process each tag across all pages using cursors
}
```

Cursor-based pagination (when the API returns a next cursor):

```php
$endpoint = $client->createEndpoint(SuluGetTagsEndpoint::class);
$cursorPaginator = $client->paginateEmbeddedCursorCollection(
    $endpoint,
    embeddedKey: 'tags',
    parameters: ['_format' => 'json'],
    baseQuery: [], // add your filters here
    limit: 100,
    initialCursor: null, // or provide a saved cursor to resume
);

foreach ($cursorPaginator as $tag) {
    // iterate until nextCursor is null
}
```

## Contribution

- PHPStan level max
- Rector config for PHP 8.2
- Coding style follows widely adopted PHP conventions
