#!/usr/bin/env bash

cwd=`dirname "${0}"`

php "${cwd}/bin/console" doctrine:cache:clear doctrine.orm.default_metadata_cache
php "${cwd}/bin/console" doctrine:migrations:diff "$@"