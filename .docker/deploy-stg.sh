#!/bin/bash

cd /var/www/stg-server

docker-compose pull web_fmc_server
docker-compose stop web_fmc_server
docker-compose up -d web_fmc_server
docker-compose exec -T web_fmc_server php artisan migrate
# docker-compose exec -T web_fmc_server php artisan migrate:refresh --seed
# docker-compose exec -T web_fmc_server compose dump-autoload