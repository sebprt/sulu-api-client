Sulu API Client — Project Guidelines for Contributors

Audience: Advanced PHP developers extending or integrating the Sulu API client library.

1. Build / Configuration
- Requirements
  - PHP: 8.2+ (tested up to 8.4). Note: Running the full test suite on PHP 8.4 may show deprecation notices from legacy exception signatures; these do not affect runtime in supported ranges.
  - Extensions: ext-json, ext-curl, ext-mbstring (enforced in composer.json).
  - Composer: install dependencies via Composer.
- Installation
  - Makefile (preferred):
    - make install — runs composer install
  - Directly:
    - composer install
- Autoloading
  - PSR-4: Sulu\ApiClient\ => src/
  - Dev tests namespace: Sulu\ApiClient\Tests\ => tests/
- HTTP Stack
  - The client uses PSR-18 (psr/http-client) and PSR-7/17. Discovery is enabled (php-http/discovery). In consumers, provide a RequestFactory and HttpClient, or rely on discovery-compatible packages (e.g., nyholm/psr7 + symfony/http-client).
- Authentication
  - Endpoints accept an implementation of Sulu\ApiClient\Auth\RequestAuthenticatorInterface to mutate outgoing requests (e.g., add Authorization headers). Provide a project-specific implementation; no default is bundled.

2. Testing
- Test Runner
  - PHPUnit 9.x (phpunit.xml at project root).
  - Execute: vendor/bin/phpunit or make test (includes coverage-text).
- Static Analysis / Refactoring (QA)
  - make qa — runs phpstan (level: max; configured in phpstan.neon) and rector (dry-run, config rector.php with LevelSetList::UP_TO_PHP_82).
- Conventions
  - Place tests under tests/ with namespace Sulu\ApiClient\Tests.
  - Use Nyholm\Psr7\Response for PSR-7 responses in tests (nyholm/psr7 is in require-dev).
  - Use Mockery for interfaces (require-dev includes mockery/mockery).
- Minimal Example: Adding and Running a New Test
  - Example target: AbstractEndpoint::parseResponse. This centralizes HTTP status handling and JSON decoding.
  - Create tests/AbstractEndpointTest.php with a concrete anonymous subclass to bypass abstract construction and inject mocks:
    - The test covers:
      - Successful JSON response decoding (200 + application/json) returns decoded array.
      - Validation branch (422) throws Sulu\ApiClient\Exception\ValidationException when API returns validation errors in JSON.
  - Example (trimmed to essentials):
    - Use Mockery to mock SerializerInterface::deserialize to return an array.
    - Construct a Nyholm\Psr7\Response with appropriate status and headers.
    - Call parseResponse and assert results or expect exceptions.
  - Commands to run:
    - vendor/bin/phpunit -v
  - After validating your change locally for this task, remove any temporary/example tests you added unless they are intended to be part of the repository. For this task we verified using two tests and removed them afterwards as requested in the issue (see the “Housekeeping” section below).

3. Development Notes (Project-Specific)
- Endpoint Design
  - AbstractEndpoint handles:
    - URL building from PATH_TEMPLATE with {placeholders} and optional query array.
    - PSR-17 Request creation via injected RequestFactory and base URL.
    - Authentication decoration via RequestAuthenticatorInterface.
    - Transport errors: PSR-18 client exceptions are wrapped in TransportException; unexpected throwables are wrapped in UnexpectedResponseException.
  - parseResponse behavior:
    - Parses JSON only if Content-Type contains application/json; invalid JSON throws InvalidJsonException.
    - 2xx: returns null for 204/205; otherwise returns decoded JSON when available; falls back to raw body string if non-JSON success; returns null if empty body.
    - Specific mappings:
      - 400 => ValidationException (errors array propagated when available)
      - 401 => UnauthorizedException
      - 403 => ForbiddenException
      - 404 => NotFoundException
      - 405 => MethodNotAllowedException
      - 409 => ConflictException
      - 412 => PreconditionFailedException
      - 415 => UnsupportedMediaTypeException
      - 422 => ValidationException (errors preserved)
      - 429 => TooManyRequestsException (includes Retry-After header message)
      - 3xx => RedirectionException (redirects are not expected in client usage)
      - 5xx => ServerErrorException (message derived from JSON "message" or truncated body)
      - fallback => UnexpectedResponseException
  - Body handling in request(): the base class does not write a body; concrete endpoints that need a request body should override request() and/or provide a helper to serialize payloads via SerializerInterface.
- Serializer
  - SerializerInterface::deserialize(payload, 'json', ?type) is used without a target type in core flows. Keep it tolerant to generic array decoding.
- Error Surfaces
  - When API returns JSON with a "message" key, it will be used for 5xx and fallback errors, improving diagnostics.
  - Invalid JSON with application/json content type is promoted to InvalidJsonException with the HTTP status attached.
- Adding New Endpoints
  - Define METHOD and PATH_TEMPLATE constants.
  - If a body is required, either override request() to attach the serialized payload and set the Content-Type header, or add a dedicated send method that prepares a PSR-7 Request accordingly before delegating to the HTTP client.
  - Keep response handling in parseResponse whenever possible so that common behavior (exceptions, null for 204, etc.) remains centralized.
- Style / Tooling
  - Static analysis: phpstan at max level; prefer precise generics and typed arrays in docblocks as needed.
  - Rector target: up to PHP 8.2. If you need 8.3/8.4 transforms, update rector.php accordingly and validate on CI/local.
  - Coding standard: friendsofphp/php-cs-fixer is available but no .php-cs-fixer config is included; follow PSR-12. If you add a config, keep it minimal and consistent with existing code.

4. Verified Commands (as of 2025-08-19)
- Install: make install (or composer install)
- Run tests: vendor/bin/phpunit -v  (or make test)
- QA: make qa
- Notes: On PHP 8.4, you may see a deprecation notice for implicit nullable in ApiException constructor; it does not affect test outcomes. Adjust signatures when raising the minimum PHP version.

5. Housekeeping for This Task
- We created a temporary test (tests/AbstractEndpointTest.php) to validate the documented process (2 tests passed). As per the task requirement, remove any temporary files you created after validation. Only .junie/guidelines.md should remain added by this change.

If you encounter discovery issues with HTTP factories/clients during integration tests, install explicit implementations (e.g., nyholm/psr7 and symfony/http-client) and/or wire them directly into endpoint constructors to avoid reliance on discovery at runtime.
