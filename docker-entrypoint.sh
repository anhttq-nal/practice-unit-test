#!/bin/bash
set -e

# Install dependencies if they don't exist
if [ ! -d "vendor" ]; then
    composer install
fi

# First arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php "$@"
fi

exec "$@" 