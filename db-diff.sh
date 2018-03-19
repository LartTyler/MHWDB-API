#!/usr/bin/env bash

php bin/console doctrine:cache:clear doctrine.orm.default_metadata_cache
php bin/console doctrine:migrations:diff "$@"