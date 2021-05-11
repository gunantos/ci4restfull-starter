<?php

namespace App\Controllers;
use \Appkita\CI4Restfull\RestfullApi;

class Home extends RestfullApi
{
	protected $model = 'UserModel';

	protected $auth = ['digest', 'key'];
	public function index()
	{
		return $this->model->findAll();
	}
}
