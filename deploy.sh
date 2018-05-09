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

sudo ./db-reset.sh latest
./cache-clear.sh

composer install -o --no-dev

php bin/console cache:warmup --env=prod --no-debug