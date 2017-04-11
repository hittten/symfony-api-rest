#!/usr/bin/env bash
composer install --no-dev --no-scripts --optimize-autoloader
php bin/symfony_requirements
php bin/console --env=prod doctrine:database:create --if-not-exists
php bin/console --env=prod doctrine:schema:update --force
php bin/console --env=prod cache:clear
php bin/console --env=prod assets:install
