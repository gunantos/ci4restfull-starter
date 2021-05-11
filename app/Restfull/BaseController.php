<?php
namespace Appkita\CI4Restfull;

use \CodeIgniter\Controller;
use \CodeIgniter\HTTP\RequestInterface;
use \CodeIgniter\HTTP\ResponseInterface;
use \Psr\Log\LoggerInterface;
use \Appkita\PHPAuth\Authentication;
use \Appkita\PHPAuth\METHOD;
use \Appkita\CI4Restfull\Auth;
use \Appkita\CI4Restfull\ErrorOutput;

abstract class BaseController extends Controller
{
    /**
     * @var array 
     */
    protected $_auth = ['jwt', 'basic', 'digest', 'key'];
    protected $auth = [];
    protected $user_api = [];
	/**
	 * @var string|null The model that holding this resource's data
	 */
	protected $modelName;

	/**
	 * @var object|null The model that holding this resource's data
	 */
	protected $model;
    private $error_auth = 'Not Authentication';
    protected $allowed_format = ['json', 'xml', 'csv'];
    protected $mustbe = null;

    private $config = [];
	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		// instantiate our model, if needed
        $this->initConfig();
        $this->initFormat();
		$this->setModel($this->modelName);
        $this->setAuth($this->auth);
        $this->authentication($this->_auth);
	}

    private function initConfig() {
        $this->config = new \Config\Restfull();
    }

    protected function initFormat(string $format = null) {
        if (empty($this->allowed_format) || sizeof($this->allowed_format) < 1) {
            if (isset($this->config->allowed_format)) {
                if (!empty($this->config->allowed_format) && sizeof($this->config->allowed_format) > 0) {
                    $this->setAllowedFormat($this->config->allowed_format);
                }
            }
        }
        if (\is_string($this->allowed_format)) {
            $this->allowed_format = [$this->allowed_format];
        }
        if (sizeof($this->allowed_format) > 0) {
			$this->setAllowedFormat($this->allowed_format);
		}
        $default_format = 'json';
        if (isset($this->config->default_format)) {
            if (empty($this->config->default_format)) {
                $default_format = $this->config->default_format;
            }
        }
        $this->setFormat($default_format);
        $formatParam = $this->getFormatParameter();
        if (!empty($formatParam)) {
            $default_format = $formatParam;
        }
        $this->setFormat($default_format);
    }

    private function getFormatParameter() {
        $_format = $this->request->getVar('format');
        if (empty($_format)) {
            $json = $this->request->getJSON();
            if (isset($json->format)) {
                $_format = $json->format;
            }
        }
        return $_format;
    }

    protected function setAllowedFormat(array $format) {
        $this->allowed_format = $format;
        return $this;
    }

    private function cekAuthType($type = 'key') {
        $user_config = $this->config->user_config;
        $PHPAUTH = new Authentication($this->config);
        $auth = Auth::init($this->config); 
        switch (\strtolower($type)) {
            case 'key':
                $user=  $PHPAUTH->auth(METHOD::KEY, function($key) {
                    return Auth::key($key);
                });
            break;
            case 'basic':
                $user = $PHPAUTH->auth(METHOD::BASIC, function($username, $password) {
                    return Auth::basic($username, $password);
                });
            break;
            case 'digest':
                $user = $PHPAUTH->auth(METHOD::DIGEST, function($username, $password) {
                    $_user = Auth::digest($username, $password);
                    if ($_user != false) {
                          return ['username'=>$_user[$user_config['username_coloumn']], 'password'=>$_user[$user_config['password_coloumn']]];
                    } else {
                        return false;
                    }
                });
            break;
            case 'jwt':
                $user = $PHPAUTH->auth(METHOD::TOKEN, function($key) {
                    return Auth::token($key);
                });
            break;
            default:
             $user=  $PHPAUTH->auth(METHOD::KEY, function($key) {
                    return Auth::key($key);
                });
        }
        if ($user == false) {
             return ErrorOutput::error401();
        }
        $whitelist = $this->cekWhitelist($user[$user_config['whitelist_coloumn']]);
        if (!$whitelist) {
            if (!$this->cekBlacklist($user[$user_config['blacklist_coloumn']])) {
                return ErrorOutput::error401('Is IP Adress blacklist from sistem, contact Administrator System');
            }
        }
        if ($this->cekPath($user[$user_config['path_coloumn']])) {
            $this->user_api = $user;
            if (\strtolower($type) == 'digest') {
                return ['username'=>$user[$user_config['username_coloumn']], 'password'=>$user[$user_config['password_coloumn']]];
            } else {
                return $this->user_api;
            }
        } else {
            return ErrorOutput::error401();
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

    protected function authentication($config = null) {
        if (!empty($config)) {
            $this->setAuth($config);
        }
        if ($this->_auth == false) {
            return true;
        }
        if (!\is_array($this->_auth)) {
            $this->_auth = [$this->_auth];
        }
        if (sizeof($this->_auth) < 1) {
            return true;
        }
        $status = false;
        $i = 0;
        while($status == false && $i < sizeof($this->_auth)) {
            $status = $this->cekAuthType($this->_auth[$i]);
            $i++;
        }
        if ($status) {
            $this->user_api = $status;
            return true;
        } else {
            return ErrorOutput::error401();
        }
    }

    protected function setAuth($auth) {
        if (\is_string($auth)) {
            $this->_auth = [$auth];
        }else if (\is_object($auth)) {
            $this->_auth = (array) $auth;
        } else{
            $this->_auth = $auth;
        }
        return $this;
    }
	/**
	 * Set or change the model this controller is bound to.
	 * Given either the name or the object, determine the other.
	 *
	 * @param object|string|null $which
	 *
	 * @return void
	 */
	public function setModel($which = null)
	{
		// save what we have been given
		if ($which)
		{
			$this->model     = is_object($which) ? $which : null;
			$this->modelName = is_object($which) ? null : $which;
		}

		// make a model object if needed
		if (empty($this->model) && ! empty($this->modelName))
		{
			if (class_exists($this->modelName))
			{
				$this->model = model($this->modelName);
			}
		}

		// determine model name if needed
		if (! empty($this->model) && empty($this->modelName))
		{
			$this->modelName = get_class($this->model);
		}
	}

    	/**
	 * Set/change the expected response representation for returned objects
	 *
	 * @param string $format
	 *
	 * @return void
	 */
	protected function setFormat(string $format = 'json')
	{
        $format = strtolower(str_replace(' ', '', $format));
		if (in_array($format, $this->allowed_format, true))
		{
			$this->format = $format;
		}
	}

}
