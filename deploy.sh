#!/usr/bin/env bash

if [[ ! -f './composer.lock' ]]; then
    echo "This script must be run from the root directory of the project."

    exit 1
elif [[ "$(id -u)" == "0" ]]; then
    "This script must not be run as the root user."
fi

git fetch
git checkout -- .
git pull

./cache-clear.sh

composer install -o --no-dev

php bin/console cache:warmup --env=prod --no-debug

echo "Deploy complete. If necessary, be sure to run './db-reset.sh latest' to bring the database up to date."