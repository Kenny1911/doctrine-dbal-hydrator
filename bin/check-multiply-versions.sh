#!/usr/bin/env bash

WORKING_DIR="$(dirname $(dirname $(realpath $0)))"
cd "${WORKING_DIR}"

for doctrine_dbal_ver in '^3.0' '^4.0'; do
    composer install &&
    composer update --with-all-dependencies \
      --with "doctrine/dbal:${doctrine_dbal_ver}" &&
    composer run checks || exit $?
done