MAKEFLAGS += --silent
PWD = $(shell pwd)
SYMFONY = symfony
COMPOSER = composer
VENDOR_BIN = ./vendor/bin
PHPUNIT = APP_ENV=test phpunit
PHP_CONSOLE = php bin/console
SYMFONY_CONSOLE = symfony console
NPM := pnpm


audit: ## Shortcut that runs make qa-audit
	make qa-audit

fix: ## Runs all QA tools on the project and fixes the issues.
	make qa-rector
	make qa-cs
	make qa-insights

lint: ## Runs all Linters. Generates reports.
	make qa-lint-twigs
	make qa-lint-yaml
	make qa-lint-container
	make qa-lint-schema

help: ## Lists all available make commands.
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

###################################
#
# Project setup
#
###################################
install-dev: ## Install php tools
	cd tools/php && $(COMPOSER) install --optimize-autoloader

install-project: ## Install project with normal dependencies
	$(COMPOSER) install --optimize-autoloader
	$(NPM) install --force --prefer-frozen-lockfile --silent
	$(NPM) run build
	make sf-rotate-keys
	$(PHP_CONSOLE) doctrine:database:create
	$(PHP_CONSOLE) doctrine:schema:update --force --complete
	$(PHP_CONSOLE) ux:icons:lock --force
	make sf-clear-cache --env=dev
	make qa-composer-validate

###################################
#
# Symfony commands
#
###################################
sf-clear-cache: ## Clear the Symfony cache.
	$(PHP_CONSOLE) cache:clear

sf-log: ## Open symfony logs
	$(SYMFONY) server:log

sf-open: ## Open symfony project in browser
	$(SYMFONY) open:local

sf-perms: ## Fix permissions issues on var folder
	chown -R www-data:www-data var
	chmod -R 777 var

sf-start: ## Start Symfony server
	$(SYMFONY) server:start

sf-start-daemon: ## Start Symfony server in daemon-mode
	$(SYMFONY) server:start -d
	make sf-open
	make sf-log

sf-stop: ## Stop Symfony server
	$(SYMFONY) server:stop

sf-keypair: ## Generate JWT keypair
	$(SYMFONY_CONSOLE) lexik:jwt:generate-keypair

sf-rotate-keys: # Rotate keys
    $(SYMFONY_CONSOLE) secrets:regenerate-app-secret .env.local
    $(SYMFONY_CONSOLE) secrets:regenerate-mercure-jwt-secret .env.local
    $(SYMFONY_CONSOLE) secrets:regenerate-jwt-passphrase .env.local
	make sf-keypair

###################################
#
# Quality Assurance
#
###################################

qa-audit: ## Runs a security audit (security-checker + audit + rector + PHPCS + phpmetrics + phpinsights + PHPStan). Only generates reports.#Runs a security audit (security-checker + audit + rector + PHPCS + phpmetrics + phpinsights + PHPStan). Only generates reports.
	make qa-security-checker
	$(COMPOSER) audit
	$(COMPOSER) run-script rector-bs
	$(COMPOSER) run-script cs-bs
	$(COMPOSER) run-script metrics
	$(COMPOSER) run-script stan

qa-composer-outdated: ## Check outdated dependencies.
	$(COMPOSER) outdated --direct --strict

qa-composer-validate: ## Validates a composer.json and composer.lock.
	$(COMPOSER) validate --strict --no-check-lock

qa-cs: ## Runs PHP_CodeSniffer on the project and fixes the issues.
	$(COMPOSER) run-script cs

qa-cs-bs: ## Runs PHP_CodeSniffer on the project. Generates reports.
	$(COMPOSER) run-script cs-bs

qa-insights: ## Runs PHPInsights on the project.
	$(COMPOSER) run-script insights

qa-lint-container: ## Checks the Symfony DI container for errors.
	$(SYMFONY_CONSOLE) lint:container

qa-lint-schema: ## Validates that the Doctrine schema is in sync with the current mapping metadata.
	$(SYMFONY_CONSOLE) doctrine:schema:validate --skip-sync -v --no-interaction

qa-lint-twigs: # Lints Twig templates.
	$(SYMFONY_CONSOLE) lint:twig ./templates

qa-lint-yaml: ## Lints YAML files.
	$(SYMFONY_CONSOLE) lint:yaml ./config

qa-metrics: ## Runs PHPMetrics on the project. Generates reports.
	$(COMPOSER) run-script metrics

qa-rector: ## Runs Rector on the project and fixes the issues.
	$(COMPOSER) run-script rector

qa-rector-bs: ## Runs Rector on the project. Generates reports.
	$(COMPOSER) run-script rector-bs

qa-security-checker: ## Checks security issues in your project dependencies.
	$(SYMFONY) check:security

qa-stan: ## Runs PHPStan on the project.
	$(COMPOSER) run-script stan
	
test: ## Run PHPUnit tests
	php $(VENDOR_BIN)/phpunit

test-coverage: ## Run PHPUnit tests with coverage
	php $(VENDOR_BIN)/phpunit --coverage-html docs/tools-reports/coverage --coverage-clover docs/tools-reports/clover.xml
