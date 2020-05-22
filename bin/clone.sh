#!/usr/bin/env bash

set -e

if [[ "$0" != "./bin/clone.sh" ]]
then
    echo "usage: bash ./bin/clone.sh as root path"

    exit 1
fi

TMP_DIR="/tmp/boxunphp-library"
rm -rf $TMP_DIR;
mkdir $TMP_DIR;

for REMOTE in Instance Request Response Router Config View Exception Logger Utils Session Redis Memcached Cache Mysql Model
do
    echo ""
    echo ""
    echo "Cloning $REMOTE";

    REMOTE_URL="git@github.com:boxunphp/php-${REMOTE}.git"

    cd $TMP_DIR;
    git clone $REMOTE_URL $REMOTE

    cd $REMOTE;
    git checkout master;

done