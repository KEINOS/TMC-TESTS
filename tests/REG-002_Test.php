<?php
namespace TMC\Sandbox\REG002;
/**
 * REG-002 の動作確認テスト.
 * 
 * REG-002: ファイル名は `TFC-[YYYYMMDD].json` のフォーマットであること
 */

const REG_ID  = 'REG-002';
const SUCCESS = 0; //実行ステータス 0 = 成功
const FAILURE = 2; //実行ステータス 2 = 失敗（REG の ID）

class Reg002Test extends \TMC\Sandbox\TestCase
{
    /**
     * TEST STDIN（標準入力渡しのテスト）.
     * @dataProvider dataProvider
     */
    public function testReg002_STDIN($data, $return_value_expect)
    {
        $result       = $this->runScriptWithSTDIN(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_ng'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * TEST ARG（引数渡しのテスト）.
     * @dataProvider dataProvider
     */
    public function testReg002_ARG($data, $return_value_expect)
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
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = SUCCESS;
        $expect_ng = FAILURE;

        // データ・セット初期化
        $data_set = [];

        // 標準テスト
        $data_set['regular'] = [
            [
                'TMC-20190101.json',
                'TMC-20190102.json',
                'TMC-20190103.json',
            ],
            $expect_ok
        ];

        // 接頭辞のテスト
        $data_set['Prefix of header'] = [
            [
                'ABC-20190101.json',
                'tmc-20190101.json',
                'abc-20190101.json',
            ],
            $expect_ng
        ];

        // 拡張子のテスト
        $data_set['File extension'] = [
            [
                'TMC-20190101.JSON',
                'TMC-20190101.jsn',
                'TMC-20190101.PNG',
            ],
            $expect_ng
        ];

        // 日付のテスト
        $data_set['Date format'] = [
            [
                'TMC-2019010.json',
                'TMC-2019.json',
            ],
            $expect_ng
        ];

        // 全体のフォーマット
        $data_set['multi bite spaces'] = [
            [
                'TMC-20190101.json',
                'TMC-あ20190101.json',
                ' TMC-20190101.json ',
            ],
            $expect_ng
        ];

        // ZWSP（文字列内にゼロ幅スペース）
        $data_set['zero width sapce in between'] = [
            [
                'TMC-​20190101​.json',
            ],
            $expect_ng
        ];

        // ZWSP（文字列の前後にゼロ幅スペース）
        $data_set['zero width sapce before and after'] = [
            [
                '​TMC-20190101.json​',
            ],
            $expect_ng
        ];

        return $data_set;
    }
}
