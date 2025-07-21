<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Sulu\ApiClient\SuluClient;
use Sulu\ApiClient\Http\ClientOptions;
use Sulu\ApiClient\Exception\ApiException;

// Create client options
$options = new ClientOptions();
$options->setTimeout(30);
$options->setVerifySsl(true);

// Create a client instance with cookie-based authentication (using SULUSESSID)
$client = new SuluClient('https://localhost:8000/', 'foduek2467j42785rnj13pqjsm', $options);

try {
    // Get a list of pages
    $pages = $client->pages()->getList([
        'locale' => 'fr',
        'limit' => 10,
        'page' => 1
    ]);

    echo "Found " . count($pages['_embedded']['pages']) . " pages\n";
//
//    // Get a specific page
//    $page = $client->pages()->getByUuid('page-uuid', ['locale' => 'en']);
//
//    echo "Page title: " . $page['title'] . "\n";
//
//    // Create a new page
//    $newPage = $client->pages()->create([
//        'title' => 'New Page',
//        'template' => 'default',
//        'parent' => 'parent-uuid',
//        'locale' => 'en'
//    ]);
//
//    echo "Created new page with UUID: " . $newPage['id'] . "\n";
//
//    // Update a page
//    $updatedPage = $client->pages()->update($newPage['id'], [
//        'title' => 'Updated Page Title',
//        'locale' => 'en'
//    ]);
//
//    echo "Updated page title to: " . $updatedPage['title'] . "\n";
//
//    // Get media items
//    $media = $client->media()->getList([
//        'limit' => 10,
//        'page' => 1
//    ]);
//
//    echo "Found " . count($media['_embedded']['media']) . " media items\n";
//
//    // Get categories
//    $categories = $client->categories()->getList();
//
//    echo "Found " . count($categories['_embedded']['categories']) . " categories\n";
//
//    // Get current user
//    $currentUser = $client->user()->me();
//
//    echo "Current user: " . $currentUser['username'] . "\n";

} catch (ApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";

    if ($e->getResponseData()) {
        echo "Response data: " . json_encode($e->getResponseData()) . "\n";
    }
}
