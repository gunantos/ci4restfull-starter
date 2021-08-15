<?php
namespace Appkita\CI4Restfull;

use \Appkita\CI4Restfull\Cache\CacheAPI;

Class Logging {
    public static $user;
    public static $api;
    public static $config;

    public static function set(array $config, $api) {
        Logging::$config = $config;
        Logging::$api = $api;
    }

    private static function _file() {
        $path = WRITEPATH . DIRECTORY_SEPARATOR .'logs'. DIRECTORY_SEPARATOR. Logging::$config['model'];
        if (!\file_exists($path)) {
            @\mkdir($path, 0777, true);
        }
        $file = $path .DIRECTORY_SEPARATOR. date('Y-m-d') .'.log';
        $teks = "[". date('H:i:s') ."] ". \json_encode(Logging::$api->toArray());
        file_put_contents($file, $teks . PHP_EOL, FILE_APPEND);
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
        if (empty(Logging::$api)) {
            Logging::$api = Logging::initCache();
        }
        Logging::$api->end_time = microtime(true);
        if (Logging::$config['saveto'] == 'database') {
            Logging::_database();
        } else {
            Logging::_file();
        }
    }

    private static function initCache() {
        $router = service('router');
        $cache = new CacheAPI();
        $cache->controller = $router->controllerName();
        $cache->function = $router->methodName();
        $cache->ipaddress = '';
        $cache->format = '';
        $cache->start_time = \microtime(true);
        $cache->request = null;
        $cache->header = [];
        return $cache;
    }
}