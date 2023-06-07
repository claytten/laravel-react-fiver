Laravel 10 Restful API + React Fiver
===============
This repo is functionality not completed yet. I will update it soon.

## Requirement
 * `Docker Compose` [link](https://docs.docker.com/compose/install/)

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
docker-compose up --build -d
```
Grab a shell inside docker/app
```
docker-compose exec -u root app /bin/sh
```
Generate key and migrate database after grab shell
```
php artisan key:generate && php artisan migrate
```
You can now access the server at http://localhost:8022 in default port. You can change port in .env at FORWARD_NGINX_PORT.

Testing
------------
Make sure you are already push project into docker using docker-compose
Grab a shell inside docker/app
```
docker-compose exec -u root app /bin/sh
```
Run this code to testing
```
php artisan test
```

## Dependencies
 * [laravel/sanctum](https://github.com/spatie/laravel-permission) `V2.15`

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

## License
Laravel 10 Restful API + React Fiver is open-sourced software licensed under the [MIT license].