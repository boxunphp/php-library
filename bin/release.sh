#!/usr/bin/env bash

set -e
set -x

if [[ "$0" != "./bin/release.sh" ]]
then
    echo "bash ./bin/release.sh as root path"

    exit 1
fi

if (( "$#" != 1 ))
then
    echo "Tag has to be provided"

    exit 1
fi

#CURRENT_BRANCH="1.x"
VERSION=$1

# Always prepend with "v"
if [[ $VERSION != v*  ]]
then
    VERSION="v$VERSION"
fi

CURRENT_DIR=`pwd`
TMP_DIR="/tmp/boxunphp-library"

for REMOTE in Instance Request Response Router Config View Exception Logger Utils Session Redis Memcached Cache Mysql Model
do
    echo ""
    echo ""
    echo "Releasing $REMOTE";

    cd "$TMP_DIR/$REMOTE";
    git tag $VERSION
    git push origin --tags

done