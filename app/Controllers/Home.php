<?php

namespace App\Controllers;
use \Appkita\CI4Restfull\RestfullApi;

class Home extends RestfullApi
{
	protected $modelName = 'UserModel';

	protected $auth = ['key'];

	private function getParameter(string $method) {
        $keyname = \str_replace(' ', '', 'x-api-key');
        switch(\strtolower(\str_replace(' ', '', $method))) {
            case 'get':
                return isset($_GET[$keyname]) ? $_GET[$keyname] : '';
            break;
            case 'post':
                return isset($_POST[$keyname]) ? $_POST[$keyname] : '';
            break;
            case 'json':
                $json = \file_get_contents('php://input');
                $data = json_decode($json);
                if (isset($data->{$keyname})) {
                    return $data->{$keyname};
                } else {
                    return '';
                }
            break;
            default:
               return '';
        }
    }
	public function index()
	{
		$i = 0;
		$allowed = ['get', 'post', 'json'];
		
        $keyname = str_replace(' ', '', 'x-api-key');
        $keyname = strtoupper(str_replace('-', '_', $keyname));
        $key_value = isset($_SERVER['HTTP_'. $keyname]) ? $_SERVER['HTTP_'. $keyname] : '';
		echo 'server='. $key_value.PHP_EOL;

		while(empty($key_value) && $i < \sizeof($allowed)) {
			 $param = \strtolower($allowed[$i]);
            $get = $this->getParameter($param);
			$key_value = !empty($get) ? $get : $key_value;
			echo $param.'='.$get.PHP_EOL;
            $i++; 
		}
        die();

		#return $this->model->findAll();
	}
}
