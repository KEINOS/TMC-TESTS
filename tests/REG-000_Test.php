<?php
namespace TMC\Sandbox\REG000;

/**
 * REG-000 の動作確認テスト・サンプル.
 *
 * REG-000: 検証データが 'Hello World!' の文字列であること。
 */

const REG_ID  = 'REG-000';
const SUCCESS = 0; //実行ステータス 0 = 成功
const FAILURE = 1; //実行ステータス 1 = 失敗

class Reg000Test extends \TMC\Sandbox\TestCase
{
    /**
     * TEST STDIN（標準入力渡しのテスト）.
     *
     * @dataProvider dataProvider
     */
    public function testReg000_STDIN($data, $return_value_expect)
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
    public function testReg000_ARG($data, $return_value_expect)
    {
        $result       = $this->runScriptWithArg(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_ng'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * DATA（テスト・データ）.
     */
    public function dataProvider()
    {
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = SUCCESS;
        $expect_ng = FAILURE;
        $control   = 'Hello World!';

        // データ・セット初期化
        $data_set = [];

        // 標準テスト
        $data_set['Regular data'] = [
            [ $control ],
            $expect_ok
        ];

        // NGテスト
        $data_set['Has space in the end'] = [
            [ 'Hello World! ' ],
            $expect_ng
        ];
        $data_set['Typo'] = [
            [ 'Hello Wolrd!' ],
            $expect_ng
        ];
        $data_set['Missing letter'] = [
            [ 'Hell World!' ],
            $expect_ng
        ];
        
        return $data_set;
    }
}
