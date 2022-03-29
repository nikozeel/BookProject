#!/bin/bash

CONTAINER_FIRST_STARTUP="CONTAINER_FIRST_STARTUP"
if [ ! -e /$CONTAINER_FIRST_STARTUP ]; then
        echo "The container is already init";
        /usr/bin/supervisord --nodaemon
else
        echo "The container is launched for the first time"
        runuser -l project -c 'cd /var/www/BookProject; composer install -q'
        echo "Remove old migrations"
        rm -f /var/www/BookProject/migrations/*
        echo "Drop previous database if any"
        APP_ENV=dev /var/www/BookProject/bin/console doctrine:database:drop --force -q
        echo "Create database";
        APP_ENV=dev /var/www/BookProject/bin/console doctrine:database:create -q
        echo "Make migration";
        APP_ENV=dev /var/www/BookProject/bin/console make:migration -q
        echo "Make migrations:migrate";
        APP_ENV=dev /var/www/BookProject/bin/console doctrine:migrations:migrate -q
        echo "Seeding the database";
        APP_ENV=prod /var/www/BookProject/bin/console app:sandbox
        echo $'\nCache prod clean';
        APP_ENV=prod /var/www/BookProject/bin/console cache:clear -q
        echo "Cache prod warmup";
        APP_ENV=prod /var/www/BookProject/bin/console cache:warmup -q
        rm /$CONTAINER_FIRST_STARTUP

        /usr/bin/supervisord --nodaemon 
fi
