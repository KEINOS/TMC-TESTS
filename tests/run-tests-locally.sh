#!/usr/bin/env bash

cd $(cd $(dirname $0); pwd)

## パスの設定
path_vol_host=$(dirname $(pwd))
path_vol_guest='/app'
name_dir_tests='tests'
path_dir_tests="${path_vol_guest}/${name_dir_tests}"

## テストの実行
docker run \
  --rm \
  -v $path_vol_host:$path_vol_guest \
  --workdir $path_dir_tests \
  phpunit/phpunit:latest \
    --configuration "${path_dir_tests}/phpunit.xml" \
    $path_dir_tests
