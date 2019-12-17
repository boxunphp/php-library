#!/usr/bin/env bash

set -e

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

CURRENT_DIR=`pwd`
#CURRENT_BRANCH="1.x"
VERSION=$1

# Always prepend with "v"
if [[ $VERSION != v*  ]]
then
    VERSION="v$VERSION"
fi

SRC_DIR="${CURRENT_DIR}/src"
TMP_DIR="/tmp/boxunphp-library"
rm -rf "$CURRENT_DIR/tests";
mkdir "$CURRENT_DIR/tests";

    for REMOTE in Request Response Router Config View Exception Logger Utils Session Error Redis Memcached Cache Mysql Model
    do
        echo ""
        echo ""
        echo "Releasing $REMOTE";

        REMOTE_URL="git@github.com:boxunphp/php-${REMOTE}.git"

        cd $TMP_DIR;
        git clone $REMOTE_URL $REMOTE
        (
            rm -rf "$SRC_DIR/$REMOTE";
            mkdir "$SRC_DIR/$REMOTE";
            cp -r "$TMP_DIR/$REMOTE/src/*" "$SRC_DIR/$REMOTE/";
            cp -r "$TMP_DIR/$REMOTE/tests/*" "$CURRENT_DIR/tests/";
        )

#        cd $REMOTE;
#        git checkout master;

#        git tag $VERSION
#        git push origin --tags


    done
