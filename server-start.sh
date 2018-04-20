#!/usr/bin/env bash

function usage() {
    echo "${0} [ip[:port]]"

    exit
}

listen="0.0.0.0:8000"

if [[ $# > 0 ]]; then
    listen="${1}"
fi

cwd=`dirname "${0}"`

php "${cwd}/bin/console" server:start "${listen}"