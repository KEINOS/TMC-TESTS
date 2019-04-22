#!/usr/bin/env bash

echo ' Running Circle CI localy ...'
echo '------------------------------'

# Check CircleCI CLI
which circleci > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo '- You need to install circleci CLI.'
    echo 'See: https://circleci.com/docs/ja/2.0/local-cli/'
    exit $LINENO
fi

# Check Docker
which docker > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo '- You need to install docker.'
    echo 'See: https://circleci.com/docs/ja/2.0/local-cli/'
    exit $LINENO
fi

cd $(dirname $(cd $(dirname $0); pwd)) && \
circleci local execute
result=$?
docker image prune -f > /dev/null 2>&1

exit $result
