Laravel 10 Restful API + React Fiver
===============
This repo is functionality not completed yet. I will update it soon.

## Requirement
 * `Docker Compose` [link](https://docs.docker.com/compose/install/)
 * `(optional) Make` [Windows/Mac/Linux](https://sp21.datastructur.es/materials/guides/make-install.html)

## Installation
Clone the repository
```
git clone https://github.com/claytten/laravel-react-fiver.git
```
Switch to the repo folder
```
cd laravel-react-fiver
```
Copy the example env file and make the required configuration changes in the .env file
```
cp .env.example .env
```
Push project into docker using docker-compose
```
docker-compose up --detach --build
```
Grab a shell inside docker/app
```
docker-compose exec app sh
```
Install dependencies inside container app
```
composer install
```
Generate key and migrate database
```
composer dump-autoload && php artisan key:generate && php artisan storage:link && php artisan migrate
```
You can now access the server at http://localhost:8022 in default port. You can change port in .env at FORWARD_NGINX_PORT.
***Note*** : If you already install "Make" on your machine, after build container you can run some command like "make install" to install composer without grab a shell inside docker/app first or "make migrate" to migrate database. Then you can customize whatever you want on makefile to shortcut your command.

Testing
------------
Make sure you are already push project into docker using docker-compose and already on current directory laravel-react-fiver
Copy .env and rename to .env.testing
```
cp .env .env.testing
```
Delete one line DB_DATABASE in .env.testing and change DB_DATABASE_TESTING to DB_DATABASE
```
sed -i '/^DB_DATABASE=/d' .env.testing && sed -i 's/^DB_DATABASE_TESTING=/DB_DATABASE=/' .env.testing
```
Grab a shell inside docker/app
```
docker-compose exec app sh
```
Run this code to testing
```
php artisan test --env=testing
```

## Dependencies
 * [laravel/sanctum](https://github.com/spatie/laravel-permission) `V2.15`

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

## License
Laravel 10 Restful API + React Fiver is open-sourced software licensed under the [MIT license].