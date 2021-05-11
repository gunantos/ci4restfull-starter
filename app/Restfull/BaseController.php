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
    protected $format = 'json';
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
		$this->setModel($this->modelName);
        $this->authentication($this->_auth);
        $format = $request->getVar('format');
        $format = empty($format) ? $request->getPost('format') : $format;
        $format = empty($format) ? $request->getGet('format') : $format;
        if (empty($format)) {
            $json = $request->getJSON();
            if (isset($json->format)) {
                $format = $json->format;
            }
        }
        if (!empty($format)) {
            if (\in_array(strtolower($format), $this->allowed_format)) {
                $this->setFormat($format);
            }
        }
	}

    protected function setAllowedFormat(array $format) {
        $this->allowed_format = $format;
        return $this;
    }

    private function cekAuthType($type = 'key') {
        $config = new \Config\Restfull();
        $user_config = $config->user_config;
        $PHPAUTH = new Authentication($config);
        $auth = Auth::init($config); 
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
            $class = $this->router->controllerName();
            $method = $this->router->methodName();
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
		if (in_array($format, ['json', 'xml'], true))
		{
			$this->format = $format;
		}
	}

}
