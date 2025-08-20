install:
	composer install

generate:
	@echo "Generation placeholder (use openapi generator if available)"


test:
	vendor/bin/phpunit --coverage-text

qa:
	vendor/bin/phpstan analyse --configuration=phpstan.neon
	@if [ -x vendor/bin/rector ]; then \
		vendor/bin/rector process --dry-run; \
	else \
		echo "Rector not installed, skipping"; \
	fi
