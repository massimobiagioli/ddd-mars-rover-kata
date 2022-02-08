.PHONY: default up down install test coverage start-frontend
default: help ;

help:				## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

up: 				## Starts the project
	docker-compose up -d
	cd backend && symfony server:start --port=8070 -d

down: 				## Stops the project
	docker-compose stop
	cd backend && symfony local:server:stop

install: 			## Install the project
	docker-compose up -d
	cd backend && symfony composer install
	cd backend && symfony console doctrine:database:drop --if-exists --force
	cd backend && symfony console doctrine:database:create
	cd backend && symfony console orm:schema:update --force --dump-sql
	cd backend && symfony console doctrine:migrations:migrate --no-interaction

	cd backend && symfony console doctrine:database:drop --if-exists --force --env=test
	cd backend && symfony console doctrine:database:create --env=test
	cd backend && symfony console doctrine:schema:update --force --env=test
	cd backend && symfony console doctrine:migrations:migrate --no-interaction --env=test
	docker-compose exec -it mysql_test bash -c "mysql -uapp -papp app_test < /var/www/db.sql"

test:				## Launch test
	cd backend && symfony console doctrine:database:drop --if-exists --force --env=test
	cd backend && symfony console doctrine:database:create --env=test
	cd backend && symfony console doctrine:schema:update --force --env=test
	cd backend && symfony console doctrine:migrations:migrate --no-interaction --env=test
	docker-compose exec mysql_test bash -c "mysql -uapp -papp app_test < /var/www/db.sql"
	cd backend && bin/phpunit

coverage:			## Launch test coverage
	cd backend && symfony console doctrine:database:drop --if-exists --force --env=test
	cd backend && symfony console doctrine:database:create --env=test
	cd backend && symfony console orm:schema:update --force --env=test
	cd backend && symfony console doctrine:migrations:migrate --no-interaction --env=test
	docker-compose exec mysql_test bash -c "mysql -uapp -papp app_test < /var/www/db_test.sql"
	cd backend && XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html build/coverage

psalm:				## Launch PSalm
	cd backend && ./vendor/bin/psalm

start-frontend:			## Start Frontend
	cd frontend && yarn && yarn dev
