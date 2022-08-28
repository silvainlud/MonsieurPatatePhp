isDocker := $(shell docker info > /dev/null 2>&1 && echo 1)
user := $(shell id -u)
group := $(shell id -g)


ifeq ($(isDocker), 1)
	dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose -f docker-compose.yml -f docker-compose.override.yml
	de := docker-compose exec
	dr := $(dc) run --rm
	sy := $(de) php bin/console
	node := $(dr) node
	php := $(dr) --no-deps php
else
	sy := php bin/console
	node :=
	php :=
endif


.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: dev
dev: ## Lance le serveur de développement
	$(dc) up -d


.PHONY: cmd
cmd: ## BASH dans le conteneur php
	$(dc) exec php bash

.PHONY: lint
lint: vendor/autoload.php ## Analyse le code
	$(dockerRun) ./vendor/bin/phpstan analyse  --memory-limit=-1

.PHONY: lintb
lintb: vendor/autoload.php ## Analyse le code (sans docker)
	./vendor/bin/phpstan analyse  --memory-limit=-1

.PHONY: format
format: ## Formate le code
	$(dockerRun) ./vendor/bin/phpcbf -q || exit 0
	$(dockerRun) ./vendor/bin/php-cs-fixer fix --allow-risky=yes --config ".php-cs-fixer.dist.php"

.PHONY: test
test: ## Lancer les tests unitaire
	$(dockerRun) ./bin/phpunit

.PHONY: image
image: ## Constrction d'une image docker
	docker build tools/docker/php -t git.silvain.eu/silvain.eu/monsieurpatatephp:latest
	docker push git.silvain.eu/silvain.eu/monsieurpatatephp:latest

.PHONY: php
php: ## Accéder au conteneur php
	docker run  -w /app --rm -it -e "TERM=xterm-256color" -v $(PWD):/app registry.silvain.eu:5000/silvain.eu/monsieurpatatephp:latest bash

planning_screen_sync:
	docker run --rm --user $(user):$(group) -v $(PWD)/var/data:/app/data registry.silvain.eu:5000/silvain.eu/monsieurpatateplanning:latest

deploy:
	 ansible-playbook -i tools/ansible/deploy/inventory --extra-vars "ansible_user=ludwig" --ask-vault-password tools/ansible/deploy/deploy.yml