#!/usr/bin/env bash

cwd=`dirname "${0}"`

php "${cwd}/bin/console" doctrine:migrations:migrate "$@"