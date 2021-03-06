<?php

namespace App\Controllers;
use \Appkita\CI4Restfull\RestfullApi;
use \App\Models\UserModel;

class Home extends RestfullApi
{
	#protected $modelName = 'UserModel';
	protected $model;
    protected $allowed_format = ['json'];

	protected $auth = ['key'];

	function __construct() {
		$this->model = new UserModel();
	}
	public function index()
	{
		return $this->respond(['status'=>true, 'data'=>$this->model->findAll()]);
	}

	public function show($id = null)
	{
		return $this->respond(['status'=>true, 'data'=>$this->model->find($id)]);
	}

	public function create() {
		die('create ');
	}

	public function update($id = null) {
		die('update '. $id);
	}

	public function deleted($id = null) {
		die('deleted '. $id);
	}
}
