#!/usr/bin/env bash

function usage() {
    echo "${0} SNAPSHOT [DB]"

    exit
}

if [[ $# < 1 ]]; then
    usage
fi

snapshot="${1}"
db="application"

if [[ $# > 1 ]]; then
    db="${2}"
fi

mysql -e "DROP SCHEMA ${db}; CREATE SCHEMA ${db};"
mysql "${db}" < "${snapshot}"