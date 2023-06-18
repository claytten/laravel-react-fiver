stop:
	docker-compose stop

shell:
	docker-compose exec app sh

start:
	docker-compose up --detach

install:
	docker-compose exec app composer install

destroy:
	docker-compose down --volumes

build:
	docker-compose up --detach --build

seed:
	docker-compose exec app php artisan db:seed

migrate:
	docker-compose exec app php artisan migrate:fresh

test:
	docker-compose exec app php artisan test

clean:
	docker-compose exec -u root app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app composer dump-autoload

.PHONY: stop shell start destroy build seed migrate test