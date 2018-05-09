#!/usr/bin/env bash

function usage() {
    echo "${0} snapshot [database]"
    echo
    echo "snapshot - a path to a SQL file to load, or the string 'latest' to load the most recent file from the"
    echo "           snapshots directory"
    echo "database - the schema to load the SQL file into"

    exit
}

fmtRst="\e[0m"
fmtEm="\e[1m\e[36m"

cwd=`dirname "${0}"`

if [[ $# < 1  || "${1}" == '--help' || "${1}" == '-h' ]]; then
    usage
fi

snapshot="${1}"

if [[ "${snapshot}" == "latest" ]]; then
    snapshot=`ls -vr "${cwd}/snapshots" | cut -f1 | head -n1`
    snapshot="${cwd}/snapshots/${snapshot}"
fi

db="application"

if [[ $# > 1 ]]; then
    db="${2}"
fi

echo -e "Restoring snapshot from ${fmtEm}${snapshot}${fmtRst} into ${fmtEm}${db}${fmtRst}"

mysql -e "DROP SCHEMA ${db}; CREATE SCHEMA ${db};"
mysql "${db}" < "${snapshot}"