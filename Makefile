DOCKER_COMPOSE = docker-compose
DOCKER_EXEC     = $(DOCKER_COMPOSE) exec -T
DOCKER_RUN     = $(DOCKER_COMPOSE) run --rm -T

ifdef CI_REGISTRY
EXEC_APP =
else
EXEC_APP = $(DOCKER_EXEC) app /entrypoint
endif
EXEC_PHP = $(DOCKER_EXEC) app /entrypoint php -d memory_limit=-1
EXEC_PG = $(DOCKER_EXEC) postgres
EXEC_RUBY = $(DOCKER_EXEC) ruby /entrypoint

SYMFONY = $(EXEC_PHP) bin/console
NPM = $(EXEC_APP) npm
COMPOSER = $(EXEC_PHP) /usr/local/bin/composer
WFI = $(DOCKER_EXEC) app wait-for-it

ifdef CI_REGISTRY
QA =
else
QA = docker run --rm -e PHP_CS_FIXER_IGNORE_ENV=true -v $(PWD):/project cacahouete/phpaudit:8.2.0
endif

ARTEFACTS = var/artefacts

APP_SRC = src
APPLICATION = develop
BRANCH = develop

IP_DB_PROD = 51.15.20.15

##
## Project
## -------
##

build: docker-compose.override.yml
	$(DOCKER_COMPOSE) pull --ignore-pull-failures
	COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1  $(DOCKER_COMPOSE) build --pull

kill: docker-compose.override.yml
	$(DOCKER_COMPOSE) kill || true
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

install: ## Install and start the project
install: build
	$(MAKE) start
	$(MAKE) db assets-install assets-symfony

install-clean: ## Install and start the project
install-clean: build
	$(MAKE) start
	$(MAKE) db-clean assets-install assets-symfony

reset: ## Stop and start a fresh install of the project
reset: kill
	$(MAKE) install

reset-clean: ## Stop and start a fresh install of the project
reset-clean: kill
	$(MAKE) install-clean

start: docker-compose.override.yml ## Start the project
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate

ss: ## Start Simple : start the project and install db
ss: start
	$(MAKE) db

start-deploy: ## Start only deploy dependencies
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate ruby

stop: docker-compose.override.yml ## Stop the project
	$(DOCKER_COMPOSE) stop

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf vendor var/cache

thanks:
	$(COMPOSER) thanks

.PHONY: build kill install reset start stop clean thanks

##
## Utils
## -----
##

cc: ## Do a cache clear
cc: vendor
	$(SYMFONY) cache:clear

db: ## Reset the database and load fixtures
db: vendor db-wait
	-$(SYMFONY) doctrine:database:drop --if-exists --force
	-$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet
	$(SYMFONY) hautelook:fixtures:load --no-interaction --no-bundles --quiet

db-clean: ## Reset the database
db-clean: vendor db-wait
	-$(SYMFONY) doctrine:database:drop --if-exists --force
	-$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet

db-prod: ## Reset the database with production dump
db-prod: var/cache/order-last.dump vendor db-wait
	-$(SYMFONY) doctrine:database:drop --if-exists --force
	-$(SYMFONY) doctrine:database:create --if-not-exists
	$(EXEC_PG) pg_restore --create --clean --no-acl --no-owner --role=runner -d postgres -U runner var/cache/order-last.dump
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet

var/cache/order-last.dump:
	scp alexandrevinet@$(IP_DB_PROD):/srv/proarti/order/prod/shared/postgresql/order-last.dump var/cache/order-last.dump

db-test: ## Reset the database for test and load fixtures
db-test: vendor db-wait
	-$(SYMFONY) doctrine:database:drop --if-exists --force --env=test
	-$(SYMFONY) doctrine:database:create --if-not-exists --env=test
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet --env=test
	$(SYMFONY) hautelook:fixtures:load --no-interaction --no-bundles --quiet --env=test

db-wait: ## Wait for db up
	@$(WFI) postgres:5432 --timeout=20

db-validate: ## Validate doctrine schema
db-validate: vendor db-wait
	$(SYMFONY) doctrine:schema:validate

db-dump: ## Dump sql diff
db-dump: vendor db-wait
	$(SYMFONY) doctrine:schema:update --dump-sql

db-diff: ## Generate a new doctrine migration
db-diff: vendor db-wait
	$(SYMFONY) doctrine:migrations:diff

db-migr: ## Run a doctrine migration
db-migr: vendor db-wait
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration

assets-symfony: ## Run symfony console to install assets
assets-symfony: vendor
	$(SYMFONY) assets:install --symlink --relative

package-lock.json: package.json
	$(NPM) install

assets-install: ## Run npm build
assets-install: package-lock.json
	$(NPM) run build

assets-dev: ## Run npm dev
assets-dev: package-lock.json
	$(NPM) run dev

assets-watch: ## Run npm watch
assets-watch: package-lock.json
	$(NPM) run watch

##
## Tests
## -----
##
test: ## Run unit and functional tests
test: phpunit behat

phpunit: ## Run unit tests
phpunit: vendor
	$(EXEC_PHP) -d extension=pcov.so bin/phpunit --exclude-group functional

behat: ## Run functional tests
behat: db-wait
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP) vendor/bin/behat --colors

# rules based on files
docker-compose.override.yml: docker-compose.override.yml.dist
ifeq ($(shell test -f docker-compose.override.yml && echo -n yes),yes)
	@echo "Your docker-compose.override.yml is outdated."
	@while [ -z "$$CONTINUE" ]; do \
		read -r -p "# Do you want to refresh your docker-compose.override.yml ? [y/N] : " CONTINUE; \
	done ; \
	if [ $$CONTINUE = "y" ] || [ $$CONTINUE = "Y" ]; then \
		cp docker-compose.override.yml.dist docker-compose.override.yml ; \
		echo "=> Refresh done" ; \
	fi
else
	cp -n docker-compose.override.yml.dist docker-compose.override.yml
endif

update: composer.json
	$(COMPOSER) update --no-interaction

composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
ifdef CI_REGISTRY
	@echo No composer install in CI
else
	$(COMPOSER) install
endif

.PHONY: tests test-ci test-ci-phpunit test-ci-behat tu tf update

##
## Quality assurance
## -----------------
##

qa:
	$(MAKE) cs-fix
	$(MAKE) stan

lint: ## Lints twig and yaml files
lint: lt ly

lt: vendor
	$(SYMFONY) cache:clear
	$(SYMFONY) lint:twig src
	$(SYMFONY) lint:twig templates

ly: vendor
	$(SYMFONY) lint:yaml src --parse-tags
	$(SYMFONY) lint:yaml config --parse-tags

security: ## Check security of your dependencies (https://security.sensiolabs.org/)
	docker run --rm -v $(PWD):/app cacahouete/local-php-security-checker-docker

phploc: ## PHPLoc (https://github.com/sebastianbergmann/phploc)
	$(QA) phploc $(APP_SRC)/

pdepend: ## PHP_Depend (https://pdepend.org)
pdepend: artefacts
	$(QA) pdepend \
		--summary-xml=$(ARTEFACTS)/pdepend_summary.xml \
		--jdepend-chart=$(ARTEFACTS)/pdepend_jdepend.svg \
		--overview-pyramid=$(ARTEFACTS)/pdepend_pyramid.svg \
		src/

md: ## PHP Mess Detector (https://phpmd.org)
	$(QA) phpmd $(APP_SRC) text .phpmd.xml

phpcodesnifer: ## PHP_CodeSnifer (https://github.com/squizlabs/PHP_CodeSniffer)
#	$(QA) phpcs --standard=./vendor/escapestudios/symfony2-coding-standard/Symfony/ --report-full $(APP_SRC)
	$(QA) phpcs --standard=.phpcs.xml --report-full $(APP_SRC)

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
	$(QA) phpcpd $(APP_SRC)

phpdcd: ## PHP Dead Code Detector (https://github.com/sebastianbergmann/phpdcd)
	$(QA) phpdcd $(APP_SRC)

phpmetrics: ## PhpMetrics (http://www.phpmetrics.org)
phpmetrics: artefacts
	$(QA) phpmetrics --report-html=$(ARTEFACTS)/phpmetrics $(APP_SRC)

stan: ## twig code style
	$(QA) php -d memory_limit=50000M /usr/local/src/vendor/bin/phpstan.phar analyse

twigcs: ## twig code style
	$(QA) twigcs lint src

cs: ## php-cs-fixer (http://cs.sensiolabs.org)
	$(QA) php-cs-fixer fix --dry-run --using-cache=no --verbose --diff

cs-fix: ## apply php-cs-fixer fixes
	$(QA) php-cs-fixer fix

.PHONY: lint lt ly phploc pdepend phpmd phpcodesnifer phpcpd phpdcd phpmetrics cs apply-cs artefacts

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
