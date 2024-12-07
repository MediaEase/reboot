#---Symfony-Justfile---------------#
# Author: https://github.com/tomcdj71
# License: MIT
#---------------------------------------------#

# Variables
set shell := ["bash", "-c"]
PWD := "`pwd`"
NPM := "pnpm"
SYMFONY := "symfony"
COMPOSER := "composer"
VENDOR_BIN := "./vendor/bin"
PHPUNIT := "APP_ENV=test phpunit"
PHP_CONSOLE := "php bin/console"
SYMFONY_CONSOLE := "symfony console"

# Shortcut that runs make qa-audit.
audit:
    just qa-audit

# Runs all QA tools on the project and fixes the issues.
fix:
    just qa-rector
    just qa-cs
    just qa-insights

# Runs all Linters. Generates reports.
lint:
    just qa-lint-twigs
    just qa-lint-yaml
    just qa-lint-container
    just qa-lint-schema

# Lists all available just commands.
help: 
    @just --list

###################################
#
# Project setup
#
###################################
# Install php tools
install-dev:
    {{COMPOSER}} install --optimize-autoloader --dev

# Install project with normal dependencies
install-project:
    {{COMPOSER}} install --optimize-autoloader
    {{NPM}} install --force --prefer-frozen-lockfile --silent
    {{NPM}} run build
    just sf-rotate-keys
    {{PHP_CONSOLE}} doctrine:database:create
    {{PHP_CONSOLE}} doctrine:schema:update --force --complete
    {{PHP_CONSOLE}} ux:icons:lock --force
    {{PHP_CONSOLE}} harmony:secrets:regenerate-app-secret .env.local
	{{PHP_CONSOLE}} harmony:secrets:regenerate-jwt-passphrase .env.local
	{{PHP_CONSOLE}} harmony:secrets:regenerate-mercure-jwt-secret .env.local
    just sf-clear-cache-dev
    just qa-composer-outdated

###################################
#
# Symfony commands
#
###################################
# Clear the Symfony cache.
sf-clear-cache:
    {{SYMFONY_CONSOLE}} cache:clear

# Clear the Symfony cache in dev environment.
sf-clear-cache-dev:
    {{SYMFONY_CONSOLE}} cache:clear --env=dev

# Open symfony logs
sf-log:
    {{SYMFONY_CONSOLE}} server:log

# Open symfony project in browser
sf-open:
    {{SYMFONY}} open:local

sf-restart:
    just sf-stop
    sleep 5
    just sf-start-daemon
    just sf-open
    {{NPM}} run dev --watch

# Fix permissions issues on var folder
sf-perms:
    chown -R www-data:www-data var
    chmod -R 777 var

# Start Symfony server
sf-start:
    {{SYMFONY}} server:start

# Start Symfony server in daemon-mode
sf-start-daemon:
    {{SYMFONY}} server:start -d

# Stop Symfony server
sf-stop:
    {{SYMFONY}} server:stop

# Generate JWT keypair
sf-keypair: #
	{{SYMFONY_CONSOLE}} lexik:jwt:generate-keypair

# Rotate keys
sf-rotate-keys:
    {{SYMFONY_CONSOLE}} secrets:regenerate-app-secret .env.local
    {{SYMFONY_CONSOLE}} secrets:regenerate-mercure-jwt-secret .env.local
    {{SYMFONY_CONSOLE}} secrets:regenerate-jwt-passphrase .env.local
    just sf-keypair

###################################
#
# Quality Assurance
#
###################################
# Runs a security audit (security-checker + audit + rector + PHPCS + phpmetrics + phpinsights + PHPStan). Only generates reports.
qa-audit:
    just qa-security-checker
    {{COMPOSER}} audit
    {{COMPOSER}} run-script rector-bs
    {{COMPOSER}} run-script cs-bs
    {{COMPOSER}} run-script metrics
    {{COMPOSER}} run-script stan

# Check outdated dependencies.
qa-composer-outdated:
    {{COMPOSER}} outdated --direct --strict

# Validates a composer.json and composer.lock.
qa-composer-validate: 
	{{COMPOSER}} validate --strict --no-check-lock

# Runs PHP_CodeSniffer on the project and fixes the issues.
qa-cs:
    {{COMPOSER}} run-script cs 

# Runs PHP_CodeSniffer on the project. Generates reports.
qa-cs-bs:
    {{COMPOSER}} run-script cs-bs

# Runs PHPInsights on the project.
qa-insights:
    {{COMPOSER}} run-script insights

# Checks the Symfony DI container for errors.
qa-lint-container:
    {{SYMFONY_CONSOLE}} lint:container

# Validates that the Doctrine schema is in sync with the current mapping metadata.
qa-lint-schema:
    {{SYMFONY_CONSOLE}} doctrine:schema:validate --skip-sync -v --no-interaction

# Lints Twig templates.
qa-lint-twigs:
    {{SYMFONY_CONSOLE}} lint:twig ./templates

# Lints YAML files.
qa-lint-yaml:
    {{SYMFONY_CONSOLE}} lint:yaml --format json config/

# Runs PHPMetrics on the project. Generates reports.
qa-metrics:
    {{COMPOSER}} run-script metrics-src

# Runs Rector on the project and fixes the issues.
qa-rector:
    {{COMPOSER}} run-script rector

# Runs Rector on the project. Generates reports.
qa-rector-bs:
    {{COMPOSER}} run-script rector-bs

# Checks security issues in your project dependencies.
qa-security-checker: 
    symfony check:security

# Runs PHPStan on the project.
qa-stan:
    {{COMPOSER}} run-script stan

# Run PHPUnit tests
test:
    php {{VENDOR_BIN}}/phpunit
# Run PHPUnit tests with coverage
test-coverage:
    php {{VENDOR_BIN}}/phpunit --coverage-html docs/tools-reports/coverage
