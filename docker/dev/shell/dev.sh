#!/bin/sh

if [ -f .env.testing ]; then
  sed -i 's/DB_HOST=.*/DB_HOST=database/' .env.testing
  sed -i 's/DB_USERNAME=.*/DB_USERNAME=dockerUser/' .env.testing
  sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env.testing
  sed -i 's/DB_DATABASE=.*/DB_DATABASE=testing_laravel_react_fiver/' .env.testing
  php artisan key:generate --env=testing
fi

composer install
php artisan key:generate
php artisan storage:link
php artisan cache:clear
composer dump-autoload