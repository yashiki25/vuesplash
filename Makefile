.PHONY: ps
ps:
	docker ps

.PHONY: up
up:
	docker-compose up -d

.PHONY: build
build:
	docker-compose build --no-cache --force-rm

.PHONY: install-packages
install-packages:
	docker-compose exec app composer require --dev barryvdh/laravel-ide-helper
	docker-compose exec app composer require --dev barryvdh/laravel-debugbar
	docker-compose exec app composer require --dev phpstan/phpstan

.PHONY: init
init:
	cp .env.example .env
	docker-compose up -d --build
	@make composer-install
	@make yarn-install
	docker-compose exec app php artisan key:generate
	@echo Initialize Completed !!

.PHONY: stop
stop:
	docker-compose stop

.PHONY: down
down:
	docker-compose down

.PHONY: restart
restart:
	@make down
	@make up

.PHONY: destroy
destroy:
	docker-compose down --rmi all --volumes

.PHONY: destroy-volumes
destroy-volumes:
	docker-compose down --volumes

.PHONY: nginx
nginx:
	docker-compose exec nginx bash

.PHONY: app
app:
	docker-compose exec app bash

.PHONY: node
node:
	docker-compose exec node sh

.PHONY: asset
asset:
	docker-compose exec asset_watcher bash

.PHONY: mysql
mysql:
	docker-compose exec mysql bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'

.PHONY: redis
redis:
	docker-compose exec redis redis-cli

.PHONY: migrate
migrate:
	docker-compose exec app php artisan migrate

.PHONY: rollback
rollback:
	docker-compose exec app php artisan migrate:rollback

.PHONY: fresh
fresh:
	docker-compose exec app php artisan migrate:fresh

.PHONY: seed
seed:
	docker-compose exec app php artisan db:seed

.PHONY: tinker
tinker:
	docker-compose exec app php artisan tinker

.PHONY: composer-install
composer-install:
	docker-compose exec app composer install

.PHONY: yarn-install
yarn-install:
	docker-compose exec node yarn

.PHONY: config
config-clear:
	docker-compose exec app php artisan config:clear

.PHONY: ide-helper
ide-helper:
	docker-compose exec app php artisan clear-compiled
	docker-compose exec app php artisan ide-helper:generate
	docker-compose exec app php artisan ide-helper:meta
	docker-compose exec app php artisan ide-helper:models --nowrite
