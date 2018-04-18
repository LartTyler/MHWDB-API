#!/usr/bin/env bash

git fetch
git checkout -- .
git pull

./db-reset.sh latest
./cache-clear.sh

composer install -o --no-dev

php bin/console cache:warmup --env=prod --no-debug