#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="1.x"

#function split()
#{
#    SHA1=`./bin/splitsh-lite --prefix=$1`
#    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
#}

function remote()
{
    git remote add $1 "git@github.com:boxunphp/$1.git" || true
}

git pull origin $CURRENT_BRANCH

for REMOTE in Request Response Router Config View Exception Logger Utils Session Error Redis Memcached Cache Mysql Model
do
    remote $REMOTE
#    git remote rm $REMOTE
done
