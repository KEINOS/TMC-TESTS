[![CircleCI](https://circleci.com/gh/KEINOS/TMC-TESTS.svg?style=svg)](https://circleci.com/gh/KEINOS/TMC-TESTS) [![Build Status](https://travis-ci.org/KEINOS/TMC-TESTS.svg?branch=master)](https://travis-ci.org/KEINOS/TMC-TESTS)

# TMC-TESTS（TMC-DATA の仕様チェック・コマンド）

このリポジトリは、**TMC-DATA のデータを検証するコマンドのリポジトリ**です。以下の情報を提供しています。

1. 「[仕様定義](./REGULATION.md)」
    - TMC-DATA のデータ形式の仕様を定義しています。各々の仕様には ID が振られています。（`REG-XXX`）
2. 「確認コマンド」（`REG-XXX` スクリプト）
    - 仕様に準拠しているかの確認用コマンドです。１つの仕様に対し同名の１つの確認コマンドが用意されています。この確認コマンドに検証データを渡すと、データが仕様に準拠しているか確認できます。
3. 「動作テスト・スクリプト」（`tests/REG-XXX_Test.php` スクリプト）
    - 上記、確認コマンドの動作テスト用のスクリプトです。コマンドが正しく動作しているかのユニット・テストに使われます。

## 確認コマンドの基本的な使い方

- 確認コマンド（`REG-XXX` のコマンド）に検証データを渡すと、仕様に準拠しているか確認できます。
- 実行ステータスが `0` の場合は正常（準拠）で、`1` 以上はエラーです。

```bash
$ # REG-003 の準拠チェック（標準入力渡し）
$ echo 'サンプルデータ' | ./REG-003 -
$ echo $?
0
$ # REG-003 の準拠チェック（引数渡し）
$ ./REG-003 'サンプルデータ'
$ echo $?
0
```

## 仕様について

### 確認コマンドに渡すデータの仕様について

TMC-DATA リポジトリにデータを追加し `PR`（`Pull Request`、「変更の反映リクエスト」のこと。プルリク）を行うと、仕様に準拠しているかの自動テストが実行されます。

その際に各データは、このリポジトリにある「確認コマンド」に通され、全てのテストにパスしないとマージ（変更の反映リクエストは適用）されません。そのため、TMC-DATA リポジトリにデータを追加する場合は、以下の仕様に準拠している必要があります。

- 仕様の定義： [REGULATION.md](./REGULATION.md)

### 仕様の確認コマンド

必須の仕様に関しては、各仕様ごとに確認用のコマンドを用意しています。このディレクトリにある「REG-XXX」スクリプトです。

各確認コマンドは「標準入力」もしくは「引数」で渡されたデータを検証し、結果をスクリプトの終了コードで返します。
エラーの場合（検証をパスしなかった場合）は、標準出力でエラー内容も出力されます。

- 終了コード `0`: 合格
- 終了コード `1`: 不合格

```bash
$ # REG-003 の準拠チェック（合格）
$ echo 'サンプルデータ' | ./REG-003
$ echo $?
0
$ # REG-005 の準拠チェック（不合格）
$ echo 'サンプルデータ' | ./REG-005
NG [Not a valid JSON data fromat.][REG-005]
$ echo $?
1
```

### スクリプトによる確認コマンドの使用例

```bash
function test(){
    echo -n "$(basename $1) ... "
    $1 "$2"
    echo [ $？ -eq 0 ] && 'OK' || 'NG'
}

# Test REG-001
DATA='{"meta":["date":"2019-03-26"]}'
test "./tests/REG-001.php" "$DATA"
```

## ディレクトリ構成

```text
.
├── LICENSE
├── README.md      // このファイル
├── .circleci
│   ├── config.yml
│   └── run-ci-locally.sh // ローカルで CI テストを行うスクリプト
├── .gitignore
├── REG-000        // 確認コマンドのサンプル
├── REG-001        // 必須仕様 REG-001 に準拠しているかの確認コマンド
├── REG-002        // 必須仕様 REG-002 に準拠しているかの確認コマンド
├── ...
├── REG-xxx        // 必須仕様 REG-xxx に準拠しているかの確認コマンド
├── REGULATION.md  // 仕様の定義書
└── tests
    ├── README.md
    ├── REG-000_Test.php
    ├── REG-001_Test.php // REG-001 確認コマンドの動作テスト
    ├── REG-002_Test.php // REG-002 確認コマンドの動作テスト
    ├── ...
    ├── REG-xxx_Test.php // REG-xxx 確認コマンドの動作テスト
    ├── autoload.php
    ├── phpunit.xml
    ├── run-tests-ci.sh      // PR 時に実行される自動テスト
    └── run-tests-locally.sh // 確認コマンドの動作テスト（テストのテスト）を実行
```
