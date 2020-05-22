#!/usr/bin/env bash

set -e
set -x

if [[ "$0" != "./bin/merge.sh" ]]
then
    echo "usage: bash ./bin/merge.sh as root path"

    exit 1
fi

CURRENT_DIR=`pwd`
TMP_DIR="/tmp/boxunphp-library"
SRC_DIR="${CURRENT_DIR}/src"
TESTS_DIR="${CURRENT_DIR}/tests"

git pull origin master
git checkout master;

for REMOTE in Instance Request Response Router Config View Exception Logger Utils Session Redis Memcached Cache Mysql Model
do
    echo ""
    echo ""
    echo "merging $REMOTE";

    rm -rf "$SRC_DIR/$REMOTE";
    cp -r "$TMP_DIR/$REMOTE/src" "$SRC_DIR/$REMOTE";

    rm -rf "$TESTS_DIR/$REMOTE";
    if [[ -d "$TMP_DIR/$REMOTE/tests/$REMOTE" ]]; then

        cp -r "$TMP_DIR/$REMOTE/tests/$REMOTE" "$TESTS_DIR/$REMOTE";
    fi

done
