# Sulu API Client

A PHP client library for the Sulu CMS API (version 2.5+). This library provides a simple and intuitive interface to interact with all aspects of the Sulu API.

## Installation

```bash
composer require sulu/api-client
```

### Requirements

- PHP 7.4 or higher
- PHP extensions:
  - json
  - curl
  - mbstring

## Basic Usage

```php
use Sulu\ApiClient\SuluClient;
use Sulu\ApiClient\Http\ClientOptions;
use Sulu\ApiClient\Exception\ApiException;

// Create client options
$options = new ClientOptions();
$options->setTimeout(30);
$options->setVerifySsl(true);

// Create a client instance with cookie-based authentication (using SULUSESSID)
$client = new SuluClient('https://your-sulu-instance.com', 'your-session-id', $options);

try {
    // Get pages
    $pages = $client->pages()->getList(['locale' => 'en']);

    // Get a specific page
    $page = $client->pages()->get('page-uuid', ['locale' => 'en']);

    // Create a page
    $newPage = $client->pages()->create([
        'title' => 'New Page',
        'template' => 'default',
        'parent' => 'parent-uuid',
        'locale' => 'en'
    ]);

    // Update a page
    $updatedPage = $client->pages()->update('page-uuid', [
        'title' => 'Updated Page Title',
        'locale' => 'en'
    ]);

    // Delete a page
    $client->pages()->delete('page-uuid');

} catch (ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Available Resources

The client provides access to the following Sulu resources:

### Pages API

```php
// Get a list of pages
$pages = $client->pages()->getList(['locale' => 'en']);

// Get a specific page
$page = $client->pages()->get('page-uuid', ['locale' => 'en']);

// Create a page
$newPage = $client->pages()->create([/* page data */]);

// Update a page
$updatedPage = $client->pages()->update('page-uuid', [/* page data */]);

// Delete a page
$client->pages()->delete('page-uuid');

// Get page children
$children = $client->pages()->getChildren('page-uuid', ['locale' => 'en']);

// Copy a page
$copiedPage = $client->pages()->copy('page-uuid', ['destination' => 'destination-uuid']);

// Move a page
$movedPage = $client->pages()->move('page-uuid', ['destination' => 'destination-uuid']);

// Publish a page
$publishedPage = $client->pages()->publish('page-uuid', ['locale' => 'en']);

// Unpublish a page
$unpublishedPage = $client->pages()->unpublish('page-uuid', ['locale' => 'en']);
```

### Media API

```php
// Get a list of media items
$media = $client->media()->getList();

// Get a specific media item
$mediaItem = $client->media()->get('media-uuid');

// Create a media item
$newMedia = $client->media()->create([/* media data */]);

// Update a media item
$updatedMedia = $client->media()->update('media-uuid', [/* media data */]);

// Delete a media item
$client->media()->delete('media-uuid');

// Upload a file
$uploadedMedia = $client->media()->upload('collection-uuid', '/path/to/file.jpg', 'File Title');

// Get media formats
$formats = $client->media()->getFormats('media-uuid');

// Get media preview URL
$previewUrl = $client->media()->getPreviewUrl('media-uuid', 'sulu-small');
```

### Categories API

```php
// Get a list of categories
$categories = $client->categories()->getList();

// Get a specific category
$category = $client->categories()->get('category-uuid');

// Create a category
$newCategory = $client->categories()->create([/* category data */]);

// Update a category
$updatedCategory = $client->categories()->update('category-uuid', [/* category data */]);

// Delete a category
$client->categories()->delete('category-uuid');

// Get category children
$children = $client->categories()->getChildren('category-uuid');
```

### Other APIs

The client also provides access to the following APIs:

- Tags API (`$client->tags()`)
- Contacts API (`$client->contacts()`)
- Accounts API (`$client->accounts()`)
- Snippets API (`$client->snippets()`)
- Webspaces API (`$client->webspaces()`)
- Languages API (`$client->languages()`)
- User API (`$client->user()`)
- Roles API (`$client->roles()`)
- Permissions API (`$client->permissions()`)

Each API provides methods for listing, getting, creating, updating, and deleting resources, as well as specialized methods for specific operations.

## Configuration

You can configure the client with additional options using the `ClientOptions` class:

```php
use Sulu\ApiClient\SuluClient;
use Sulu\ApiClient\Http\ClientOptions;

$options = new ClientOptions();
$options->setTimeout(30);        // Set request timeout in seconds
$options->setVerifySsl(false);   // Disable SSL verification (not recommended for production)

// Create a client instance with cookie-based authentication (using SULUSESSID)
$client = new SuluClient('https://your-sulu-instance.com', 'your-session-id', $options);
```

### Authentication

The client uses cookie-based authentication with the SULUSESSID cookie:

```php
$client = new SuluClient('https://your-sulu-instance.com', 'your-session-id', $options);
```

The second parameter is the session ID value for the SULUSESSID cookie. This is the same cookie that Sulu uses for browser-based authentication.

## Error Handling

The client throws `ApiException` when an API request fails. You can catch this exception to handle errors:

```php
use Sulu\ApiClient\Exception\ApiException;

try {
    $page = $client->pages()->get('non-existent-uuid');
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage();

    // Get response data if available
    if ($e->getResponseData()) {
        print_r($e->getResponseData());
    }
}
```

## License

This library is released under the MIT License.

## Development

### Running Tests

The library comes with a comprehensive test suite. To run the tests, use the following command:

```bash
vendor/bin/phpunit
```

This will run all tests in the `tests` directory. You can also run specific test classes or methods:

```bash
# Run a specific test class
vendor/bin/phpunit tests/SuluClientTest.php

# Run a specific test method
vendor/bin/phpunit --filter testConstructor tests/SuluClientTest.php
```

### Code Style

The library follows the PSR-12 coding standard. You can check and fix code style issues using PHP-CS-Fixer:

```bash
# Check code style
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style issues
vendor/bin/php-cs-fixer fix
```

### Static Analysis

You can run PHPStan for static analysis:

```bash
vendor/bin/phpstan analyse src tests
```

### Code Sniffer

The library uses PHP_CodeSniffer to check coding standards:

```bash
# Check coding standards
vendor/bin/phpcs src tests

# Fix coding standards issues
vendor/bin/phpcbf src tests
```

### Debugging

For debugging, you can use Symfony VarDumper:

```php
dump($variable); // Displays the variable
dd($variable);   // Displays the variable and exits
```

## Dependencies

This library depends on the following packages:

- guzzlehttp/guzzle: HTTP client for making API requests
- guzzlehttp/psr7: PSR-7 HTTP message implementation
- symfony/http-client: Symfony HTTP client for additional flexibility
- symfony/http-foundation: Symfony HTTP foundation components
- PSR interfaces: For HTTP client, factory, and message standards

For development, it uses:

- PHPUnit: For testing
- PHPStan: For static analysis
- PHP-CS-Fixer: For code style fixing
- Mockery: For mocking in tests
- PHP_CodeSniffer: For coding standards checking
- Symfony VarDumper: For improved debugging
