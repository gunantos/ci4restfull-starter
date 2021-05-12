<?php
namespace Appkita\CI4Restfull;
use \Appkita\CI4Restfull\Enity\CacheAPI;

Class Logging {
    public static $user;
    public static $api;
    public static $config;

    public function set(array $config, Api $api) {
        Logging::$config = $config;
        Logging::$api = $api;
    }

    private static function _file() {
        $path = WRITEPATH . DIRECTORY_SEPERATOR . Logging::$config['model'];
        if (\file_exists($path)) {
            @\mkdir($path, 0777, true);
        }
        $file = date('Y-m-d') .'_apilog.log';
        $teks = "[". date('H:i:s') ."] ". \json_encode(Logging::$api->toArray()).PHP_EOL;
        file_put_contents($file, $teks, FILE_APPEND);
    }

    private static function _database() {
        $modelname = "\\Codeigniter\Model\\".Logging::$config['model'];
        if (class_exists($modelname)) {
            $model = new $modelname;
            $api = [];
            foreach(Logging::$api->toArray() as $key =>$value) {
                if (!\is_string($value)) {
                    $value = \json_encode($value);
                }
                array_push($api, [$key => $value]);
            }
            $model->insert($api);
        }
        return false;
    }

    public static function create() {
        Logging::$api->end_time = time();
        if (Logging::$config['saveto'] == 'database') {
            Logging::_database();
        } else {
            Logging::_file();
        }
    }
}