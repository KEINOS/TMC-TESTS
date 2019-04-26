<?php
/**
 * REG-001 の動作テスト.
 *
 * REG-001:同一ディレクトリ内で目視上同じファイル名が重複しないこと（末尾にスペースがあるなど）
 */

namespace TMC\Sandbox\REG001;

const SUCCESS = 0; //実行ステータス 0 = 成功
const FAILURE = 1; //実行ステータス 1 = 失敗（REG の ID）
const REG_ID  = 'REG-001';

class Reg001Test extends \TMC\Sandbox\TestCase
{
    /**
     * TEST STDIN（標準入力渡しのテスト）.
     *
     * @dataProvider dataProvider
     */
    public function testReg001_STDIN($data, $return_value_expect)
    {
        $result       = $this->runScriptWithSTDIN(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_ng'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * TEST ARG（引数渡しのテスト）.
     *
     * @dataProvider dataProvider
     */
    public function testReg001_ARG($data, $return_value_expect)
    {
        $result       = $this->runScriptWithArg(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_ng'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * DATA STDIN（標準入力渡し用のテスト・データ各種）.
     */
    public function dataProvider()
    {
        // データ・コンテナ
        $data_set = [];
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = SUCCESS;
        $expect_ng = FAILURE;

        // 標準テスト
        $data_set['regular'] = [
            $this->getListFilesAsString(),
            $expect_ok
        ];

        // 完全同一名のテスト
        $data_set['exact same file name'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'LICENSE',
                'LICENSE', //Duplicate
            ]),
            $expect_ng
        ];

        // 大文字小文字の違い
        $data_set['case sensitive'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'LICENSE',
                'LICENse', //Duplicate
            ]),
            $expect_ng
        ];

        // 全角スペース
        $data_set['multi bite spaces'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'LICENSE',
                'LICENSE　', //Duplicate
            ]),
            $expect_ng
        ];

        // 半角スペース
        $data_set['spaces before and after'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'LICENSE',
                ' LICENSE ', //Duplicate
            ]),
            $expect_ng
        ];

        // ZWSP（文字列内にゼロ幅スペース）
        $data_set['zero width sapce in between'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'SAMPLE',
                'SAM​P​LE', //Duplicate
            ]),
            $expect_ng
        ];

        // ZWSP（文字列の前後ゼロ幅スペース）
        $data_set['zero width sapce before and after'] = [
            $this->implodeWithSingleQuote(PHP_EOL, [
                'SAMPLE',
                '​SAMPLE​', //Duplicate
            ]),
            $expect_ng
        ];

        return $data_set;
    }

    /**
     * implode 時にエスケープする
     */
    public function implodeWithSingleQuote($glue, $array){
        return implode($glue, $array);
    }

    /**
     * 現在のディレクトリのファイル一覧を正規データとして取得.
     *
     * @return  ファイル一覧を改行区切りの１文字列として返します。
     */
    public function getListFilesAsString()
    {
        $obj_iterator = new \DirectoryIterator(dirname(__FILE__));
        $list_files   = [];
        foreach ($obj_iterator as $file) {
            $list_files[] = $file->getFilename();
        }

        return $this->implodeWithSingleQuote(PHP_EOL, $list_files);
    }
}
