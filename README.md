# CodeIgniter 4 Restfull API Application Starter


Codeigniter 4 Restfull is the creation of Restfull API with the codeigniter 4 framework. Use is very simple and easy to use. And support with 4 types of security authentication ex. JWT, Basic, Key, Token

## Installation & updates

Now is Beta Version
```sh
composer create-project appkita/ci4restfull-starter@v1.0-BETA
composer update
```

## Setup

- Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

`or`

- Open Folder `App/Config/Restfull` and edit 

- Create new Controller extends `RestfullApi` and followed Restf
```php
  <?php
    namespace App\Controllers;
    class Home extends RestfullApi 
    {
       protected $auth = ['digest', 'basic', 'key', 'token'] //if you using multi authentication on controler 
       /** or 
       * protected $aut = 'basic';
       */
       
       $model = YourModelDB;
       $format = 'json';
       
       public function index {
          return $this->respond(['status'=>200, 'data'=>$this->model->findAll());
       }
       
       public function show($id = null) 
       {
          return $this->respond(['status'=>200, 'data'=>$this->model->find($id));
       }
       
       /**
         * Create a new resource object, from "posted" parameters
         *
         * @return mixed
         */
       public function create(){}
            /**
       * Return the editable properties of a resource object
       *
       * @param mixed $id
       *
       * @return mixed
       */
      public function edit($id = null){}
      
        /**
       * Add or update a model resource, from "posted" properties
       *
       * @param mixed $id
       *
       * @return mixed
       */
      public function update($id = null){}
      /**
	    * Delete the designated resource object from the model
	    *
	    * @param mixed $id
	    *
	    * @return mixed
	    */
	    public function delete($id = null){}
      
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
  
  
## Important Change with index.php

**Please** read the user guide for a better explanation of how CI4 works!


## Server Requirements

PHP version 7.3 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- xml (enabled by default - don't turn it off)
