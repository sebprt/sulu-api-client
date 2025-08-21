# Utilisation de base

## Opérations CRUD

Le client API Sulu fournit toutes les opérations CRUD standard :

### Create (Création)

```php
use Sulu\ApiClient\Endpoint\SuluPostContactEndpoint;

// Créer un nouveau contact
$endpoint = new SuluPostContactEndpoint(/* ... dépendances ... */);

$contactData = [
    'firstName' => 'Jean',
    'lastName' => 'Dupont',
    'email' => 'jean.dupont@exemple.com'
];

$newContact = $client->create($endpoint, [], [], $contactData);
echo "Contact créé avec l'ID : " . $newContact['id'];
```

### Read (Lecture)

```php
use Sulu\ApiClient\Endpoint\SuluGetContactEndpoint;

// Lire un contact spécifique
$endpoint = new SuluGetContactEndpoint(/* ... dépendances ... */);
$parameters = ['id' => 123];

$contact = $client->read($endpoint, $parameters);
echo "Contact : " . $contact['firstName'] . " " . $contact['lastName'];
```

### Update (Mise à jour)

```php
use Sulu\ApiClient\Endpoint\SuluPutContactEndpoint;

// Mettre à jour un contact
$endpoint = new SuluPutContactEndpoint(/* ... dépendances ... */);
$parameters = ['id' => 123];

$updatedData = [
    'firstName' => 'Jean',
    'lastName' => 'Martin',
    'email' => 'jean.martin@exemple.com'
];

$updatedContact = $client->update($endpoint, $parameters, [], $updatedData);
```

### Upsert (Créer ou mettre à jour)

```php
use Sulu\ApiClient\Endpoint\SuluPutContactEndpoint;

// Créer ou mettre à jour selon l'existence
$endpoint = new SuluPutContactEndpoint(/* ... dépendances ... */);
$parameters = ['id' => 123];

$contactData = [
    'firstName' => 'Jean',
    'lastName' => 'Nouveau',
    'email' => 'jean.nouveau@exemple.com'
];

$result = $client->upsert($endpoint, $parameters, [], $contactData);
```

### Delete (Suppression)

```php
use Sulu\ApiClient\Endpoint\SuluDeleteContactEndpoint;

// Supprimer un contact
$endpoint = new SuluDeleteContactEndpoint(/* ... dépendances ... */);
$parameters = ['id' => 123];

$client->delete($endpoint, $parameters);
echo "Contact supprimé";
```

## Collections et listes

### Récupération de collections

```php
use Sulu\ApiClient\Endpoint\SuluGetContactsEndpoint;

// Obtenir une liste de contacts
$endpoint = new SuluGetContactsEndpoint(/* ... dépendances ... */);

// Sans paramètres (tous les contacts)
$contacts = $client->collection($endpoint);

// Avec paramètres de requête
$query = [
    'limit' => 20,
    'page' => 1,
    'search' => 'dupont'
];

$contacts = $client->collection($endpoint, [], $query);

foreach ($contacts['_embedded']['contacts'] as $contact) {
    echo $contact['firstName'] . " " . $contact['lastName'] . "\n";
}
```

### Collection avec clé d'intégration personnalisée

```php
// Spécifier une clé d'intégration spécifique
$accounts = $client->collection(
    $endpoint,
    [],
    $query,
    'accounts', // clé d'intégration
    50 // limite
);
```

## Utilisation de la Factory d'Endpoints

Pour simplifier la création d'endpoints, utilisez la factory :

```php
use Sulu\ApiClient\Endpoint\Factory\EndpointFactory;

// Création d'une factory (généralement via DI)
$endpointFactory = new EndpointFactory(
    $httpClient,
    $requestFactory,
    $serializer,
    $authenticator,
    $contentTypeMatcher,
    $baseUrl
);

// Récupération de la factory depuis le client
$factory = $client->getEndpointFactory();

// Création simplifiée d'endpoints
$contactEndpoint = $factory->create(SuluGetContactEndpoint::class);
$contact = $client->read($contactEndpoint, ['id' => 123]);
```

## Gestion des paramètres

### Paramètres d'URL

Les paramètres sont intégrés dans l'URL de l'endpoint :

```php
// Pour /api/contacts/{id}
$parameters = ['id' => 123];
$contact = $client->read($endpoint, $parameters);
```

### Paramètres de requête

Les paramètres de requête sont ajoutés à l'URL :

```php
// Pour /api/contacts?limit=10&search=dupont
$query = [
    'limit' => 10,
    'search' => 'dupont',
    'fields' => 'id,firstName,lastName,email'
];

$contacts = $client->collection($endpoint, [], $query);
```

## Gestion des erreurs

Le client génère des exceptions spécifiques pour différents types d'erreurs :

```php
use Sulu\ApiClient\Exception\NotFoundException;
use Sulu\ApiClient\Exception\ValidationException;
use Sulu\ApiClient\Exception\UnauthorizedException;
use Sulu\ApiClient\Exception\ServerErrorException;

try {
    $contact = $client->read($endpoint, ['id' => 999]);
} catch (NotFoundException $e) {
    echo "Contact non trouvé : " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Erreur de validation : " . $e->getMessage();
    // Récupérer les erreurs de validation détaillées
    $errors = $e->getValidationErrors();
} catch (UnauthorizedException $e) {
    echo "Non autorisé : vérifiez vos credentials";
} catch (ServerErrorException $e) {
    echo "Erreur serveur : " . $e->getMessage();
}
```

### Exceptions disponibles

- `NotFoundException` (404) - Ressource non trouvée
- `UnauthorizedException` (401) - Non autorisé
- `ForbiddenException` (403) - Interdit
- `ValidationException` (422) - Erreurs de validation
- `ConflictException` (409) - Conflit
- `TooManyRequestsException` (429) - Trop de requêtes
- `ServerErrorException` (5xx) - Erreurs serveur
- `TransportException` - Erreurs de transport/réseau

## Patterns d'utilisation courants

### Service de gestion des contacts

```php
class ContactService
{
    private ApiClient $client;
    private EndpointFactoryInterface $endpointFactory;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
        $this->endpointFactory = $client->getEndpointFactory();
    }

    public function getContact(int $id): array
    {
        $endpoint = $this->endpointFactory->create(SuluGetContactEndpoint::class);
        return $this->client->read($endpoint, ['id' => $id]);
    }

    public function createContact(array $data): array
    {
        $endpoint = $this->endpointFactory->create(SuluPostContactEndpoint::class);
        return $this->client->create($endpoint, [], [], $data);
    }

    public function searchContacts(string $search, int $limit = 20): array
    {
        $endpoint = $this->endpointFactory->create(SuluGetContactsEndpoint::class);
        $query = ['search' => $search, 'limit' => $limit];
        return $this->client->collection($endpoint, [], $query);
    }
}
```

### Gestion des réponses avec données imbriquées

```php
// Récupération des données avec relations
$query = [
    'fields' => 'id,firstName,lastName,account.name',
    'embed' => 'account'
];

$contact = $client->read($endpoint, ['id' => 123], $query);

echo $contact['firstName'] . " " . $contact['lastName'];
echo "Entreprise : " . $contact['account']['name'];
```

### Batch operations (opérations par lot)

```php
// Créer plusieurs contacts
$contactsData = [
    ['firstName' => 'Jean', 'lastName' => 'Dupont'],
    ['firstName' => 'Marie', 'lastName' => 'Martin'],
    ['firstName' => 'Pierre', 'lastName' => 'Durand']
];

$createdContacts = [];
$endpoint = $this->endpointFactory->create(SuluPostContactEndpoint::class);

foreach ($contactsData as $contactData) {
    try {
        $createdContacts[] = $this->client->create($endpoint, [], [], $contactData);
    } catch (\Exception $e) {
        echo "Erreur lors de la création : " . $e->getMessage() . "\n";
    }
}
```

## Bonnes pratiques

### 1. Réutilisation des endpoints

```php
// ❌ Incorrect - création répétée
foreach ($ids as $id) {
    $endpoint = new SuluGetContactEndpoint(/* ... */);
    $contact = $client->read($endpoint, ['id' => $id]);
}

// ✅ Correct - réutilisation
$endpoint = $this->endpointFactory->create(SuluGetContactEndpoint::class);
foreach ($ids as $id) {
    $contact = $client->read($endpoint, ['id' => $id]);
}
```

### 2. Gestion des limites de taux

```php
use Sulu\ApiClient\Exception\TooManyRequestsException;

try {
    $result = $client->read($endpoint, $parameters);
} catch (TooManyRequestsException $e) {
    // Attendre avant de réessayer
    $retryAfter = $e->getRetryAfter(); // si disponible
    sleep($retryAfter ?? 60);
    // Réessayer l'opération
}
```

### 3. Validation des données avant envoi

```php
function createContact(array $data): array
{
    // Validation basique
    if (empty($data['firstName']) || empty($data['lastName'])) {
        throw new \InvalidArgumentException('Le prénom et nom sont requis');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException('Email invalide');
    }

    $endpoint = $this->endpointFactory->create(SuluPostContactEndpoint::class);
    return $this->client->create($endpoint, [], [], $data);
}
```

Continuez avec l'[utilisation des endpoints](03-endpoints.md) pour apprendre à créer vos propres endpoints personnalisés.