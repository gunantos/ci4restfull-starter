<?php
namespace Appkita\CI4Restfull;

use \CodeIgniter\Controller;
use \CodeIgniter\HTTP\RequestInterface;
use \CodeIgniter\HTTP\ResponseInterface;
use \Psr\Log\LoggerInterface;
use \Appkita\CI4Restfull\ErrorOutput;
use Appkita\CI4Restfull\Auth\BaseAuth;
use \Appkita\CI4Restfull\Cache\CacheAPI;
use \Appkita\CI4Restfull\Cache\CacheUSER;

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

    //cache
    private $_cache_user;
    private $_cache_api;

    private $start_time;
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
        $this->start_time = microtime(true);
		parent::initController($request, $response, $logger);
		 /**
         * function initialitation configuration get from config/Restfull.php 
         */
        $this->config = new \Config\Restfull();
        $this->initFormat();
        //initialisasi model
		$this->setModel($this->modelName);
        $this->initCache();
        $auth = new BaseAuth($this->auth, $this->config, false);
        $auth->init($this->_cache_api, $this->_cache_user);
	}

    
    /**
     * Initialiasi cache api
     */
    private function initCache() {
        $router = service('router');
        $this->_cache_user = new CacheUSER($this->config->user_config);
        $this->_cache_api = new CacheAPI();
        $this->_cache_api->controller = $router->controllerName();
        $this->_cache_api->function = $router->methodName();
        $this->_cache_api->ipaddress = $this->request->getIPAddress();
        $this->_cache_api->format = $this->format;
        $this->_cache_api->start_time = $this->start_time;
        $this->_cache_api->request = $this->request->getVar() ? $this->request->getVar() : $this->request->getJSON();
        $headers = [];
        foreach($this->request->getHeaders() as $key) {
            array_push($headers, ["$key" => $this->request->getHeaderLine($key)]);
        }
        $this->_cache_api->header = $headers;
    }

    /**
     * if object call done create log file
     */

    function __destruct() {
        $log = new \Appkita\CI4Restfull\Logging();
        $log::set($this->config->logging, $this->_cache_api);
        $log::create();
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

    /**
     * get format parameter form request
     * @return string format
     */
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
