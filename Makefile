# Makefile

SHELL = /bin/bash

ifndef VERBOSE
MAKEFLAGS += --no-print-directory
endif

include $(PWD)/.env.example
-include $(PWD)/.env

.SILENT: php db node

help:
	@printf '		'
	@make print-color-blue TEXT='** Available commands **\n\n'
	@make print-color-green TEXT='install'
	@printf '			install all dependencies (composer, npm)\n'
	@make print-color-green TEXT='install-backend'
	@printf '		install backend dependencies\n'
	@make print-color-green TEXT='up'
	@printf '			start project (backend and nodejs)\n'
	@make print-color-green TEXT='up-backend'
	@printf '		start project (backend only)\n'
	@make print-color-green TEXT='down'
	@printf '			stop all containers\n'
	@make print-color-green TEXT='clear-cache'
	@printf '		reset all backend caches\n'
	@make print-color-green TEXT='sync-packages'
	@printf '		update composer and npm dependencies and run db migrates\n'
	@make print-color-green TEXT='sync-db'
	@printf '			import database into container from develop server\n'
	@make print-color-green TEXT='sync-storage'
	@printf '		download files storage from develop server\n'
	@make print-color-green TEXT='sync'
	@printf '			run sync-db, sync-packages, sync-storage\n'
	@echo '---------'
	@make print-color-green TEXT='php'
	@printf '			connect to PHP container\n'
	@make print-color-green TEXT='db'
	@printf '			connect to Database container\n'
	@make print-color-green TEXT='node'
	@printf '			connect to NodeJs container\n\n'

install: init-env up
	@make install-backend
	@make npm-install

install-backend: init-env up-backend sync-db
	@make composer-install
	@make docker-exec CONTAINER="php" CONTAINER_CMD="sh -c 'php artisan storage:link --force --relative'"
	@test ${RSYNC_SKIP} = true || make sync-storage

up: up-backend
	@make docker-compose-exec COMPOSE_CMD="up -d node"

up-backend:
	@make docker-compose-exec COMPOSE_CMD="up -d web db"

down:
	@make docker-compose-exec COMPOSE_CMD="down"

init-env:
	@test -f .env || cp .env.example .env

clear-cache: init-env up-backend
	@make docker-exec CONTAINER="php" CONTAINER_CMD="php artisan optimize:clear"

npm-install:
	@make docker-exec CONTAINER="node" CONTAINER_CMD="npm ci"

composer-install:
	@make docker-exec CONTAINER="php" CONTAINER_CMD="composer install -n"

sync: sync-db sync-packages sync-storage

sync-packages: init-env up
	@make composer-install
	@make docker-exec CONTAINER="php" CONTAINER_CMD="php artisan migrate"
	@make npm-install

sync-db:
	@printf '=== Waiting for DB server'
	@while [ -z "$$(docker compose logs db 2>&1 | grep -o 'Server socket created')" ]; \
		do printf '.'; \
		sleep 2; \
		done;
	@printf '\n=== Import data from ${DB_SRC_HOST} to ${DB_HOST} ... '
	@make docker-exec \
		CONTAINER="db" \
		CONTAINER_CMD="sh -c 'mysqldump -qQR --add-drop-table --skip-lock-tables --skip-comments --ssl -h${DB_SRC_HOST} -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}'"
	@make print-color-green TEXT='DONE\n'

sync-storage:
	@printf '=== Download storage from dev server ... '
	@make docker-exec \
		CONTAINER="php" \
		CONTAINER_CMD="sh -c 'rsync -azm --exclude=\"image/\" --delete -e \"ssh -i ${RSYNC_KEY_PATH}\" ${RSYNC_SRC_PATH} ${RSYNC_DST_PATH}'"
	@make print-color-green TEXT='DONE\n'

docker-exec:
	@docker exec -it $(APP_NAME)-$(CONTAINER) $(CONTAINER_CMD)

docker-compose-exec:
	@docker compose $(COMPOSE_CMD)

print-color-green:
	@printf '\033[0;32m${TEXT}\033[0m'

print-color-blue:
	@printf '\033[0;36m${TEXT}\033[0m'

php:
	@make docker-exec CONTAINER="php" CONTAINER_CMD="bash"

db:
	@make docker-exec CONTAINER="db" CONTAINER_CMD="bash"

node:
	@make docker-exec CONTAINER="node" CONTAINER_CMD="bash"
