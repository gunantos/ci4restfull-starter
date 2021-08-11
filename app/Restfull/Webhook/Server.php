<?php
namespace Appkita\CI4Restfull\Webhook;

class Server {
    protected $config = null;
    protected $pid = null;

    function __construct() {
       $path_config = \dirname(\dirname(\dirname(__FILE__))).DIRECTORY_SEPARATOR.'Config';
       $config_file = $path_config .DIRECTORY_SEPARATOR.'Webhook.php';
       if (!\file_exists($config_file)) {
            throw new \Exception('Configuration file not found '. $config_file);
       }
       require_once $config_file;
       $this->config = new \Appkita\CI4Restfull\Webhook\Config\Webhook();
    }

    protected function shell_bg($cmd) {
        echo $cmd;
        if (substr(php_uname(), 0, 7) == "Windows"){
            $p = \popen('start /B '. $cmd, 'r');
            $this->pid = $s['pid'] ?? '';
            \pclose($p);
            return $this->pid;
        } else {
            exec($cmd . " > /dev/null &", $op);
            $this->pid = (int)$op[0];
            return $this->pid;
        }
    }

    public function run($port = null) {
        if (empty($port)) {
            $port = $this->config->port;
        }
        $path = \dirname(__FILE__) .'www';
        $cmd = 'cd '. $path .' && php -S 0.0.0.0:'. $port;
        $this->pid = $this->shell_bg($cmd);
        return $this->pid;
    }

    public function stop() {
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }

    public function status(){
        $command = 'ps -p '.$this->pid;
        \exec($command, $op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function GetCpuUsage()
    {
        exec("ps aux | grep ".$this->pid." | grep -v grep | grep -v su | awk {'print $3'}", $return);
        return $return;
    }

    public function GetMemUsage()
    {
        exec("ps aux | grep ".$this->pid." | grep -v grep | grep -v su | awk {'print $4'}", $return);
        return $return;
    }
}