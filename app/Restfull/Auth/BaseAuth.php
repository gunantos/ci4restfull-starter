<?php
namespace Appkita\CI4Restfull\Auth;
use \Appkita\PHPAuth\Authentication;
use \Appkita\CI4Restfull\ErrorOutput;
use \Appkita\PHPAuth\METHOD;
use \CodeIgniter\HTTP\RequestInterface;
use \Appkita\CI4Restfull\Auth\Cek;

class BaseAuth {
    private $_auth = '';
    private $config = null;

    public function __construct($auth, $config) { 
        $this->_auth = self::setAuth($auth);
        $this->config = $config;
    }

    /**
     * reinit authentication type
     */
    protected function setAuth($auth) {
        if (\is_string($auth)) {
            return [$auth];
        }else if (\is_object($auth)) {
            return (array) $auth;
        } else{
            return $auth;
        }
    }    
    /**
     * function init() is cek authentication api
     */
    public function init(&$cache_api, &$cache_user) {
        if ($this->_auth == false) {
            return true;
        }
        if (sizeof($this->_auth) < 1) {
            return true;
        }
        $user = false;
        $i = 0;
        while($user == false && $i < sizeof($this->_auth)) {
            $user = $this->cekAuthType($this->_auth[$i], $cache_api, $cache_user);
            $i++;
        }
        if ($user) {    
            //Get user configuration
            $user_config = $this->config->user_config;        
            $cache_api->user = (isset($user[$this->config->user_config['username_coloumn']]) 
                                ? $user[$this->config->user_config['username_coloumn']]
                                : '');
            $whitelist = $this->cekWhitelist($user[$user_config['whitelist_coloumn']]);
            /**
             * if whitelist is set
             */
            if (!$whitelist) {
                if (!$this->cekBlacklist($user[$user_config['blacklist_coloumn']])) {
                    return ErrorOutput::error401('Is IP Adress blacklist from sistem, contact Administrator System');
                }
            }
            if ($this->cekPath($user[$user_config['path_coloumn']])) {
                if (\strtolower($type) == 'digest') {
                    $this->_user_api = ['username'=>$user[$user_config['username_coloumn']], 'password'=>$user[$user_config['password_coloumn']]];
                } else {
                    $this->_user_api = $user;
                }
                return true;
            } else {
                return ErrorOutput::error401();
            }
        } else {
             return ErrorOutput::error401();
        }
    }

    /**
     * cek auth type 
     * @param String $type = is auth type
     */
    private function cekAuthType($type = 'key', &$cache_api, &$cache_user) {
        $user_config = $this->config->user_config;
        $PHPAUTH = new Authentication($this->config);
        $auth = Cek::init($this->config); 
        $user = null;
        switch (\strtolower($type)) {
            case 'key':
                $user=  $PHPAUTH->auth(METHOD::KEY, function($key) {
                    $_user = Cek::key($key);
                    if ($_user) {
                        $cache_user->auth = 'key';
                        $cache_api->auth = 'key';
                        $this->createCacheUser($_user);
                        return $_user;
                    } else {
                        return false;
                    }
                });
            break;
            case 'basic':
                $user = $PHPAUTH->auth(METHOD::BASIC, function($username, $password) {
                    $_user =  Cek::basic($username, $password);
                    if ($_user) {
                        $cache_user->auth = 'basic';
                        $cache_api->auth = 'basic';
                        $this->createCacheUser($_user);
                        return $_user;
                    } else {
                        return false;
                    }
                });
            break;
            case 'digest':
                $user = $PHPAUTH->auth(METHOD::DIGEST, function($username, $password) {
                    $_user = Cek::digest($username, $password);
                    if ($_user != false) {
                          
                        $cache_user->auth = 'digest';
                        $cache_api->auth = 'digest';
                          $this->createCacheUser($_user);
                          return ['username'=>$_user[$user_config['username_coloumn']], 'password'=>$_user[$user_config['password_coloumn']]];
                    } else {
                        return false;
                    }
                });
            break;
            case 'jwt':
                $user = $PHPAUTH->auth(METHOD::TOKEN, function($key) {
                    $_user = Cek::token($key);
                    if ($_user) {
                       
                        $cache_user->auth = 'jwt';
                        $cache_api->auth = 'jwt';
                        $this->createCacheUser($_user);
                        return $_user;
                    } else {
                        return false;
                    }
                });
            break;
            default:
             $user=  $PHPAUTH->auth(METHOD::KEY, function($key) {
                     $_user = Cek::key($key);
                    if ($_user) {
                        
                        $cache_user->auth = 'key';
                        $cache_api->auth = 'key';
                        $this->createCacheUser($_user);
                        return $_user;
                    } else {
                        return false;
                    }
                });
        }
        return $user;
    }
    
    private function createCacheUser($user) {
        if (\is_array($user)) {
            foreach($user as $key => $value) {
                $cache_user->{$key} = $value;
            }
        }
    }

    private function cekBlacklist($list) {
        $ip = $this->request->getIPAddress();
        if (\in_array($ip, $list)) {
            return false;
        } else {
            return true;
        }
    }

    private function cekWhitelist($list) {
        $ip = $this->request->getIPAddress();
        if (\in_array($ip, $list)) {
            return true;
        } else {
            return false;
        }
    }  

    private function cekPath($list) {
        if ($list == '*') {
            return true;
        } else if(\is_array($list)) {
            $router = service('router');
            $class = $router->controllerName();
            $method = $router->methodName();
            $class = \str_replace(' ', '', $class);
            $class = \str_replace(APPPNAMESPACE, '', $class);
            $class = \str_replace('Controllers', '', $class);
            $class = \str_replace('controllers', '', $class);
            $class = \str_replace('\\', '', $class);
            $path = $class.'_'.$method;
            if (\in_array(\strtolower($path), $list)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}