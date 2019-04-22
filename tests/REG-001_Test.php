<?php
/**
 * REG-001 の動作テスト.
 *
 * REG-001:同一ディレクトリ内で目視上同じファイル名が重複しないこと（末尾にスペースがあるなど）
 */
 namespace TMC\Sandbox\REG001;

const DIR_SEP = \DIRECTORY_SEPARATOR;
const SUCCESS = 0; // 実行ステータス 0 = 成功, 1 =< 失敗
const FAILURE = 2; //実行ステータス 2 = 失敗（REG の ID）
const REG_ID  = 'REG-001';

class Reg001Test extends \TMC\Sandbox\TestCase
{
    /**
     * TEST STDIN（標準入力渡しのテスト）.
     *
     * @dataProvider dataProviderForSTDIN
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
     * @dataProvider dataProviderForArg
     */
    public function testReg001_ARG($data, $return_value_expect)
    {
        $result       = $this->runScriptWithArg(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_ng'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * DATA ARG（引数渡し用のテスト・データ各種）.
     */
    public function dataProviderForArg()
    {
        // データ・コンテナ
        $data_set = [];
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = 0;
        $expect_ng = 1;

        // 標準テスト（既存ディレクトリでテスト）
        $path_dir_search = dirname(__FILE__);
        $data_set['regular'] = [
            [
                $path_dir_search,
            ],
            $expect_ok
        ];

        // ダミーのディレクトリパスでテスト
        $path_dir_search = $path_dir_search . 'dummy';
        $data_set['Dummy directory'] = [
            [
                $path_dir_search,
            ],
            $expect_ng
        ];

        return $data_set;
    }

    /**
     * DATA STDIN（標準入力渡し用のテスト・データ各種）.
     */
    public function dataProviderForSTDIN()
    {
        // データ・コンテナ
        $data_set = [];
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = 0;
        $expect_ng = 1;

        // 標準テスト
        $data_set['regular'] = [
            [
                'LICENSE',
                'README.md',
                'REG-001',
            ],
            $expect_ok
        ];

        // 完全同一名のテスト
        $data_set['exact same file name'] = [
            [
                'LICENSE',
                'LICENSE', //Duplicate
            ],
            $expect_ng
        ];

        // 大文字小文字の違い
        $data_set['case sensitive'] = [
            [
                'LICENSE',
                'LICENse', //Duplicate
            ],
            $expect_ng
        ];

        // 全角スペース
        $data_set['multi bite spaces'] = [
            [
                'LICENSE',
                'LICENSE　', //Duplicate
            ],
            $expect_ng
        ];

        // 半角スペース
        $data_set['multi bite spaces'] = [
            [
                'LICENSE',
                ' LICENSE ', //Duplicate
            ],
            $expect_ng
        ];

        // ZWSP（文字列内にゼロ幅スペース）
        $data_set['zero width sapce in between'] = [
            [
                'SAMPLE',
                'SAM​P​LE', //Duplicate
            ],
            $expect_ng
        ];

        // ZWSP（文字列の前後ゼロ幅スペース）
        $data_set['zero width sapce before and after'] = [
            [
                'SAMPLE',
                '​SAMPLE​', //Duplicate
            ],
            $expect_ng
        ];

        return $data_set;
    }
}
