# Tests for tests（テストのためのテスト）

このディレクトリには、１階層上のディレクトリにある**確認コマンド（仕様チェックするスクリプト、「REG-XXX」）の動作をテストするためのスクリプト**が置かれています。

各「REG-XXX」スクリプトに対し、「REG-XXX_Test.php」が、各々の仕様を網羅しているかをテストしています。

## テストの実行

### ローカル・テスト

各確認コマンドの**動作テストを事前にローカルで行う**には、このリポジトリのルートから以下のコマンドを実行します。

```bash
./tests/run-tests-locally.sh
```

このローカル・テストには、[`PHPUnit` がインストールされた `docker` イメージ](https://hub.docker.com/r/phpunit/phpunit
)を利用しています。そのため、あらかじめ `docker` がインストールされている必要があります。

- macOS の場合は [Homebrew（`brew cask` コマンド）](https://brew.sh/index_ja) でインストールすると楽です。（要 `docker` アカウント）
  - `$ brew cask install docker`
- [PHPUnit](https://phpunit.de/index.html) がグローバル・インストールされている場合は、`docker` を利用せずにテスト（ユニット・テストの実行）が可能です。`tests` ディレクトリで以下のコマンドを実行してください。

  ```
  $ cd tests
  $ phpunit
  ```

- 関連サイト
  - [https://phpunit.de/](https://phpunit.de/index.html) @ PHPUnit 公式サイト
  - [https://www.docker.com/](https://www.docker.com/) @ Docker 公式サイト


### ローカル・CI テスト

新規で作成した「確認コマンド」（`REG-XXX`）のスクリプト、およびその「動作テスト」スクリプト（`REG-XXX_Test.php`）を PR（`Pull Request`, プルリク）すると CI（自動的にテスト）が実行されます。このテストをパスしないと、PR はマージ（変更は適用）されません。

事前にローカルで CI テスト
場合は、このリポジトリのルートから以下のコマンドを実行します。

```bash
./.circleci/run-ci-locally.sh
```

ローカルの CI テストには [`CircleCI CLI` を利用しています](https://circleci.com/docs/2.0/local-cli/)。そのため、あらかじめ `docker` と `CircleCI-CLI` がインストールされている必要があります。

- macOS の場合は [Homebrew（`brew cask` コマンド）](https://brew.sh/index_ja) でインストールすると楽です。
  - `$ brew install --ignore-dependencies circleci`

- 関連サイト
  - [https://circleci.com/](https://circleci.com/) @ Circle CI 公式サイト

## テストの作成ルール

- １つの仕様チェック用スクリプト（「REG-XXX」ファイル）に対し、１テストファイルとすること。（ファイル内のテスト数は任意）
- テストファイル名は仕様 ID に「`_Test.php`」のサフィックスをつけること。（「`REG-XXX_Test.php`」）
  - 例）`REG-001` コマンド用のテスト場合は `REG-001_Tests.php`
- 2019/04/16 現在、動作テストは PHP7 でのみ行なっています。`REG-000_Test.php` を参考に作成してください。
