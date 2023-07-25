# == Executables =====
DOCKER_COMP = docker compose
PHP         = php
COMPOSER    = composer
SYMFONY     = symfony console
YARN        = yarn

# == Help =====
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

# == Project setup =====
setup: install setup-database setup-frontend ## Setup the whole project

setup-dev: install setup-database setup-fixtures setup-frontend-dev ## Setup the project in dev environment

install: ## Install composer dependencies
	@$(COMPOSER) install --no-interaction

setup-frontend: ## Setup the frontend via yarn
	@$(YARN) install
	@$(YARN) encore production

setup-frontend-dev: ## Setup the frontend via yarn (dev environment)
	@$(YARN) install
	@$(YARN) encore dev

setup-database: ## Setup the database backend
	@$(SYMFONY) doctrine:database:create --if-not-exists --no-interaction
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

setup-fixtures: ## Install the fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

reset-database: ## Reset the whole database (caution!)
	@$(SYMFONY) doctrine:database:drop --force
	@$(SYMFONY) doctrine:database:create --no-interaction
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

test-database: ## Setup the test database
	bin/console doctrine:database:create --no-interaction --if-not-exists --env=test
	bin/console doctrine:schema:create --env=test

# == Docker setup ===
start: build up ## Build and start the containers

build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

# == Project pipelines =====
checks: validate phpcsfixer phpstan psalm prettier eslint ## Run static checks pipeline

ci: checks tests ## Run CI pipeline

reset: install reset-database setup-fixtures setup-frontend-dev ## Reset pipeline for the whole project (caution!)

tests: phpunit ## Run test pipeline

# == Project shortcuts =====
eslint:
	@$(YARN) run eslint assets

messenger:
	@$(SYMFONY) messenger:consume async -vvv

phpcsfixer:
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix

phpmd:
	vendor/bin/phpmd src/ html phpmd.xml --report-file var/build/phpmd.html --ignore-violations-on-exit

phpunit:
	vendor/bin/phpunit

phpstan:
	vendor/bin/phpstan analyse

prettier:
	@$(YARN) run prettier --check .

psalm:
	vendor/bin/psalm

validate:
	@$(COMPOSER) validate
