#!/usr/bin/env bash

git fetch
git checkout -- .
git pull

composer install -o --no-dev

php bin/console cache:warmup --env=prod --no-debug

./db-migrate.sh