<?php
namespace TMC\Sandbox\REG000;

class Reg000Test extends \TMC\Sandbox\TestCase
{
    public function testReg000()
    {
        $path_dir_reg = '..';
        $id_reg  = 'REG-000';

        $message = 'Hello World!';
        $lastline = exec("${path_dir_reg}/${id_reg} '${message}' 2>&1", $output, $return_var);
        $this->assertTrue($return_var === 0, $lastline);

        $message = 'Hello World! ';
        $lastline = exec("${path_dir_reg}/${id_reg} '${message}' 2>&1", $output, $return_var);
        $this->assertTrue($return_var === 1, $lastline);

        $message = 'Hell World!';
        $lastline = exec("${path_dir_reg}/${id_reg} '${message}' 2>&1", $output, $return_var);
        $this->assertTrue($return_var === 1, $lastline);
    }
}
