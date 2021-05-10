<?php

namespace App\Controllers;
use \Appkita\CI4Restfull\RestfullApi;

class Home extends RestfullApi
{
	protected $auth = ['digest', 'key'];
	public function index()
	{
		return view('welcome_message');
	}
}
