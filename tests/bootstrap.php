<?php
namespace TMC\Sandbox;

const DIR_SEP = \DIRECTORY_SEPARATOR;
const SUCCESS = 0; //終了ステータス（成功）
const FAILURE = 1; //終了ステータス（失敗）

class TestCase extends \PHPUnit\Framework\TestCase
{
/*
    public function setUp()
    {
        // Warning も確実にエラーとして扱うようにする
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $msg  = 'Error #' . $errno . ': ';
            $msg .= $errstr . " on line " . $errline . " in file " . $errfile;
            throw new \RuntimeException($msg);
        });
    }
    public function tearDown()
    {
        restore_error_handler();
    }
*/
    protected function getRealPathReg($name_file_command)
    {
        static $paths_file_reg;

        if (isset($paths_file_reg[$name_file_command])) {
            return $paths_file_reg[$name_file_command];
        }

        $path_dir_script = dirname(__FILE__);
        $path_dir_parent = dirname($path_dir_script);

        $paths_file_reg[$name_file_command] = $path_dir_parent . DIR_SEP . $name_file_command;

        return $paths_file_reg[$name_file_command];
    }

    protected function printError($msg)
    {
        fputs(STDERR, addslashes($msg) . PHP_EOL);
    }

    protected function continueIfPathIsFile($path_file_script)
    {
        if (! file_exists($path_file_script)) {
            $this->printError('File not found at: ' . $path_file_script . PHP_EOL);
            exit(FAILURE);
        }

        if (! is_file($path_file_script)) {
            $this->printError('Path is not a file: ' . $path_file_script . PHP_EOL);
            exit(FAILURE);
        }
        return true;
    }

    protected function continueIfArray($array)
    {
        if (! is_array($array)) {
            $this->printError('Input data must be a array.' . PHP_EOL);
            exit(FAILURE);
        }
        return true;
    }

    protected function implodeArray(array $array)
    {
        $array = array_filter($array, 'strlen'); // Remove empty element
        $array = array_values($array);           // Renumber keys
        return implode(PHP_EOL, $array);
    }

    protected function explodeString($string)
    {
        $lines = explode(PHP_EOL, $string);
        #$lines = array_filter($lines, 'strlen'); // Remove empty element
        #$lines = array_values($lines);           // Renumber keys
        return $lines;
    }

    protected function redirectStdOutToStdIn($command)
    {
        if (false === strpos($command, '2>&1')) {
            return $command . ' 2>&1&';
        }
        return $command;
    }

    protected function runCommand($command)
    {
        $command   = $this->redirectStdOutToStdIn($command);
        $lastline  = exec($command, $output, $return_var);
        $result    = empty($lastline) ? implode(PHP_EOL, $output) : $lastline;
        $result_ok = ($return_var === 0) ? $result : '';
        $result_ng = ($return_var === 0) ? '' : $result;

        return [
            'return_value' => $return_var,
            'result_ok'    => $result_ok,
            'result_ng'    => $result_ng,
        ];
    }

    protected function runScriptWithArg($id, $data)
    {
        $path_file_script = $this->getRealPathReg($id);
        $this->continueIfPathIsFile($path_file_script);

        if (is_array($data)) {
            $data = $this->implodeArray($data);
        }

        // テスト実行
        $command   = "${path_file_script} '${data}' 2>&1";

        return $this->runCommand($command);
    }

    protected function runScriptWithSTDIN($id, $data)
    {
        $path_file_script = $this->getRealPathReg($id);
        $this->continueIfPathIsFile($path_file_script);
        $this->continueIfArray($data);

        $path_dir_script = dirname($path_file_script);
        $path_dir_parent = dirname($path_dir_script);

        $descriptor_spec = [
            0 => ["pipe", "r"], //STDIN
            1 => ["pipe", "w"], //STDOUT
            2 => ["pipe", "w"], //STDERR
        ];
        $command = $path_file_script . ' -'; // Add command option for STDIN
        $process = proc_open($command, $descriptor_spec, $pipes, $path_dir_parent);
        if (! is_resource($process)) {
            $this->printError('Can not open command: ' . $path_file_script . PHP_EOL);
            return false;
        }

        #$lines = $this->implodeString($data);

        foreach ($data as $line) {
            fwrite($pipes[0], $line . PHP_EOL); //Don't forget to line feed
        }
        fclose($pipes[0]);

        $result_ok = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $result_ng = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        return [
            'return_value' => $return_value,
            'result_ok'    => $result_ok,
            'result_ng'    => $result_ng,
        ];
    }
}