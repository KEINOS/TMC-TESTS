<?php
/**
 * Test for REG-003.
 * REG-003 : データは UTF-8 であること
 */
namespace TMC\Sandbox\REG003;

const SUCCESS = 0; //実行ステータス 0 = 成功
const FAILURE = 3; //実行ステータス 3 = 失敗（REG の ID）
const REG_ID  = 'REG-003';

class Reg003Test extends \TMC\Sandbox\TestCase
{
    /**
     * TEST STDIN（標準入力渡しのテスト）.
     * @dataProvider dataProvider
     */
    public function testReg003_STDIN($data, $return_value_expect)
    {
        $result       = $this->runScriptWithSTDIN(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_msg'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * TEST ARG（引数渡しのテスト）.
     * @dataProvider dataProvider
     */
    public function testReg003_ARG($data, $return_value_expect)
    {
        $result       = $this->runScriptWithArg(REG_ID, $data);
        $return_value = $result['return_value'];
        $msg_error    = $result['result_msg'];
        $this->assertTrue($return_value === $return_value_expect, $msg_error);
    }

    /**
     * DATA（各種文字コードのテスト・データ）.
     */
    public function dataProvider()
    {
        // データ・コンテナ
        $data_set = [];
        // 終了ステータス 0 = 成功, 0 < 失敗
        $expect_ok = SUCCESS;
        $expect_ng = FAILURE;

        $control = 'これは表示機能テスト用のテキスト・データです。';

        // テストデータの作成
        $list_encode = $this->getListEncode();
        foreach ($list_encode as $encode) {
            $expect   = ($encode === 'UTF-8') ? $expect_ok : $expect_ng;
            $name_key = ($encode === 'UTF-8') ? 'regular data' : 'irregular data';
            $name_key .= ' ' . $encode;
            $value    = mb_convert_encoding($control, $encode, 'UTF-8');
            $data_set[$name_key] = [
                [
                    "${value}",
                ],
                $expect
            ];
        }
        
        return $data_set;
    }

    private function getListEncode()
    {
        return [
            'UTF-8',
            'EUC-JP',
            'SJIS',
            'eucJP-win',
            'EUC-JP-2004',
            'SJIS-win',
            'SJIS-mac',
            'SJIS-2004',
            'CP932',
            'CP51932',
            'JIS',
            'ISO-2022-JP',
            'ISO-2022-JP-MS',
            'JIS-ms',
            'ISO-2022-JP-2004',
            'CP50220',
            'CP50220raw',
            'CP50221',
            'CP50222',
        ];
    }
}
