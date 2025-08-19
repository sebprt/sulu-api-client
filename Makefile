install:
	composer install

generate:
	@echo "Generation placeholder (use openapi generator if available)"


test:
	vendor/bin/phpunit --coverage-text

qa:
	vendor/bin/phpstan analyse --configuration=phpstan.neon
	vendor/bin/rector process --dry-run
