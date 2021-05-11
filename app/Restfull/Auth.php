<?php

namespace Appkita\CI4Restfull;
use \Appkita\CI4Restfull\ErrorOutput;

Class Auth {
    private static $base_config = [];
    private static $db = false;
    private static $config = '';
    
    public static function init($config) {
        Auth::$config = $config->user_config;
        Auth::$base_config = $config;
        if ($config->cekfrom == 'database') {
            Auth::$db = true;
        } else {
            Auth::$db = false;
        }
    }

    private static function dbCek($username=false, $password =false, $key =false) {
        $config = Auth::$config;
        $mdl_name = "\\App\Models\\". $config['model'];
        $db = new $mdl_name();
        $db->asArray();
        if ($username) {
            $db->where($config['username_coloumn'], $username);
        }
        if ($key) {
            $db->where($config['key_coloumn'], $key);
        }
        $user = $db->first();
        if (!$user) {
            return false;
        }
        if (empty($user)) {
            return false;
        }
        $user[$config['whitelist_coloumn']] = array_filter(\explode(';', $user[$config['whitelist_coloumn']]));
        $user[$config['blacklist_coloumn']] = array_filter(\explode(';', $user[$config['blacklist_coloumn']]));
        
        if ($user[$config['path_coloumn']] != '*') {
            $user[$config['path_coloumn']] = array_filter(\explode(';', $user[$config['path_coloumn']]));
        }
        if ($password != false) {
            if (\password_verify($password, $user[$config['password_coloumn']])) {
                return $user;
            } else {
                return ErrorOutput::error401();
            }
        } else {
            return $user;
        }
    }

    private static function fileCek($username=false, $password=false, $key =false) {
        $config = Auth::$config;
        $user_list = Auth::$base_config->{$config['model']};
        if ($username) {
            $indeks = array_search($username, array_column($user_list, $config['username_coloumn']));
        } else if ($key) {
            $indeks = array_search($key, array_column($user_list, $config['key_coloumn']));
        } else {
            return false;
        }
        $user = [];
        if ($indeks === false || $indeks < 0) {
            return false;
        }else{
            $user = $user_list[$indeks];
        }
        if ($password != false) {
            if ($user[$config['password_coloumn']] !== $password) {
                return ErrorOutput::error401();
             }
        }
        return $user;
    }

    private static function cek($username='', $password='', $key='') {
        if (Auth::$db) {
            return Auth::dbCek($username, $password, $key);
        } else {
            return Auth::fileCek($username, $password, $key);
        }
    }

    public static function key($key) {
        return Auth::cek(false, false, $key);
    }

    public static function basic($username, $password) {
        return Auth::cek($username, $password, false); 
    }

    public static function digest($username, $password = null) {
        return Auth::cek($username, $password, false); 
    }

    public static function token($username) {
        return Auth::cek($username, false, false); 
    }
}