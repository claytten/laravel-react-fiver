#!/bin/bash

# Create database with name database from .env file
mysql -uroot -p$MYSQL_ROOT_PASSWORD -s -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE_TEST; GRANT ALL PRIVILEGES ON $MYSQL_DATABASE_TEST.* TO '$MYSQL_USER'@'%'; FLUSH PRIVILEGES;";
