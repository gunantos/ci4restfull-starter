<a href="https://app-kita.com" alt="app-kita, app kita"><img src="https://app-kita.com/img/logo-teks.965d24bf.png" width="100"></a><br>
![Discord](https://img.shields.io/discord/846036920811126844?style=plastic)
[![GitHub stars](https://img.shields.io/github/stars/gunantos/ci4restfull-starter)](https://github.com/gunantos/ci4restfull-starter/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/gunantos/ci4restfull-starter)](https://github.com/gunantos/ci4restfull-starter/issues)
[![GitHub forks](https://img.shields.io/github/forks/gunantos/ci4restfull-starter)](https://github.com/gunantos/ci4restfull-starter/network)
[![GitHub license](https://img.shields.io/github/license/gunantos/ci4restfull-starter)](https://github.com/gunantos/ci4restfull-starter/blob/main/LICENSE)<br>
<a href="https://sponsor.app-kita.net" target="_blank"><img src="https://img.shields.io/github/sponsors/gunantos?logo=gunantos&style=for-the-badge" title="Pay Coffe" /></a><br>
# CodeIgniter 4 Restfull API Application Starter

Codeigniter 4 Restfull is the creation of Restfull API with the codeigniter 4 framework. Use is very simple and easy to use. And support with 4 types of security authentication ex. JWT, Basic, Key, Token

You can manage api using database or File configuration

follow Setup Configuration!

## Installation & updates

- composer

```sh
composer create-project appkita/ci4restfull-starter
cd ci4restfull-starter
composer update
```

-manual

1.  Download latest release from `https://github.com/gunantos/ci4restfull-starter/releases`
2.  extract to public_html
3.  `composer install`

## Setup

- Copy `env` to `.env` and tailor for your app, specifically the baseURL, any database settings and Restfull setting.

`or`

- Open Folder `App/Config/Restfull` and edit

```php
   //you can set database of file
   public $cekfrom = 'file'

   //configuration user cek
  public $user_config = [
      'model' => 'UserModel', //model name or parameter if you using file
      'username_coloumn'=>'email',
      'password_coloumn'=>'password',
      'key_coloumn' => 'apikey',
      'path_coloumn'=>'path',
      'block_coloumn'=>'isblock',
      'whitelist_coloumn'=>'whitelist',
      'blacklist_coloumn'=>'blacklist'
    ];

    //if you using file $cekfrom
    $UserModel = [
   	[
        'email'=>'user@email.com',
        'password'=>'password',
        'apikey'=>'123123',
        'isblock'=>false,  //if you block return true
        'whitelist'=>[], //add whitelist ip address
        'blacklist'=>[], //add blacklist ip address
        'path'=>'*' //use * for allow all access or array controllername_methodname
      ]
     ]

    //Configuration your Header API KEY
    public $haderKey = 'X-API-KEY';

    /**
     * @var array $allowed_key_parameter
     * if you API KEY allowed get from parameter GET, POST, or JSON
     */
    public $allowed_key_parameter = ['get', 'post', 'json'];
    //configuration data include on json token
   $token_data = 'username';

   public $allowed_format = ['json', 'xml', 'csv'];

    /**
     * @var string $default_format
     */
    public $default_format = 'json';

```

- Create new Controller extends `RestfullApi`

```php
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

```

- Run application with `spark` or `host`

```sh
  //spark
  php spark serve
```

- acess api
  `http://localhost:8080` spark run
  `http://localhost/yourapi/public` xamp or wamp

## Important

**Please** read the user guide of [Codeigniter 4](https://codeigniter.com/user_guide/)

# Sponsor

[Pay Coffe](https://sponsor.app-kita.net)
