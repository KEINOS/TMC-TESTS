<?php
namespace TMC\Sandbox;

const DIR_SEP = \DIRECTORY_SEPARATOR;
const SUCCESS = 0; //終了ステータス（成功）
const FAILURE = 1; //終了ステータス（失敗）

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function addRedirectSTDERRtoSTDOUT($command)
    {
        if (false === strpos(str_replace(' ', '', $command), '2>&1')) {
            return trim($command) . ' ' . '2>&1';
        }
        return $command;
    }

    protected function continueIfArray($array)
    {
        if (! is_array($array)) {
            $this->printError('Input data must be a array.' . \PHP_EOL);
            exit(FAILURE);
        }
        return true;
    }

    protected function continueIfPathIsFile($path_file_script)
    {
        if (! file_exists($path_file_script)) {
            $this->printError('File not found at: ' . $path_file_script . \PHP_EOL);
            exit(FAILURE);
        }

        if (! is_file($path_file_script)) {
            $this->printError('Path is not a file: ' . $path_file_script . \PHP_EOL);
            exit(FAILURE);
        }
        return true;
    }

    protected function explodeString($string)
    {
        return explode(\PHP_EOL, $string);
    }

    protected function getRealPathReg($name_file_command)
    {
        static $paths_file_reg;

        if (isset($paths_file_reg[$name_file_command])) {
            return $paths_file_reg[$name_file_command];
        }

        $path_dir_script = dirname(__FILE__);
        $path_dir_parent = dirname($path_dir_script);

        $paths_file_reg[$name_file_command] = $path_dir_parent . \TMC\Sandbox\DIR_SEP . $name_file_command;

        return $paths_file_reg[$name_file_command];
    }

    protected function implodeArray(array $array)
    {
        // Remove empty element and renumbr keys.
        // Comment out below in order to give data as is
        //$array = array_filter($array, 'strlen');
        //$array = array_values($array);
        return implode(\PHP_EOL, $array);
    }

    protected function openProcess($command, $data, $path_dir_work)
    {
        $descriptor_spec = [
            0 => ["pipe", "r"], //STDIN
            1 => ["pipe", "w"], //STDOUT
            2 => ["pipe", "w"], //STDERR
        ];

        $process = proc_open($command, $descriptor_spec, $pipes, $path_dir_work);

        if (! is_resource($process)) {
            proc_close($process);
            $msg = 'Can not open command: ' . addslashes($command);
            return [
                'return_value' => false,
                'result_ok'    => '',
                'result_ng'    => $msg,
                'result_msg'   => $msg,
            ];
        }

        return $this->writeProcess($process, $data, $pipes);
    }

    protected function printError($msg)
    {
        fputs(\STDERR, addslashes($msg) . \PHP_EOL);
    }

    protected function runCommand($command)
    {
        $command    = $this->addRedirectSTDERRtoSTDOUT($command);
        $lastline   = exec($command, $output, $return_var);

        $result_msg = empty(trim($lastline)) ? implode(\PHP_EOL, $output) : $lastline;
        $result_ok  = ($return_var === SUCCESS) ? $result_msg : '';
        $result_ng  = ($return_var === SUCCESS) ? '' : $result_msg;

        return [
            'return_value' => $return_var,
            'result_ok'    => $result_ok,
            'result_ng'    => $result_ng,
            'result_msg'   => $result_msg,
        ];
    }

    protected function runScriptWithArg($id, $data)
    {
        $path_file_script = $this->getRealPathReg($id);
        $this->continueIfPathIsFile($path_file_script);

        if (is_array($data)) {
            $data = $this->implodeArray($data);
        }

        // テストの実行
        $command = "${path_file_script} '${data}' ";

        return $this->runCommand($command);
    }

    protected function runScriptWithSTDIN($id, $data)
    {
        $path_file_script = $this->getRealPathReg($id);

        $this->continueIfPathIsFile($path_file_script);

        $path_dir_script = dirname($path_file_script);
        $path_dir_parent = dirname($path_dir_script);

        $command = $path_file_script . ' -'; // Add command argument '-' as STDIN

        return $this->openProcess($command, $data, $path_dir_parent);
    }

    protected function writeProcess($process, $data, $pipes)
    {
        if(is_array($data)){
            $data = $this->implodeArray($data);
        }

        fwrite($pipes[0], $data);
        fclose($pipes[0]);

        $result_ok = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $result_ng = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        unset($process);

        return [
            'return_value' => $return_value,
            'result_ok'    => $result_ok,
            'result_ng'    => $result_ng,
            'result_msg'   => trim($result_ok) . trim($result_ng),
        ];
    }
}
