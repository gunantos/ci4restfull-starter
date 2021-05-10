<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Restfull extends BaseConfig
{
    /**
     * @var String $cekfrom | database or file
     */
    public $cekfrom = 'database';
    
    /**
     * @var String $file_config 
     * if you using file cek authentication

     public $user_config = [
      'model' => 'RestUser',
      'username_coloumn'=>'email',
      'password_coloumn'=>'password',
      'key_coloumn' => 'apikey',
      'path_coloumn'=>'path',
      'block_coloumn'=>'isblock',
      'whitelist_coloumn'=>'whitelist',
      'blacklist_coloumn'=>'blacklist'
    ];
    
     */

    public $RestUser = [
      [
        'email'=>'user@email.com',
        'password'=>'password',
        'key'=>'123123',
        'block'=>false,
        'whitelist'=>[],
        'blacklist'=>[],
        'path'=>'*'
      ]
    ];
     /**
      * @var string $database_config
      * insert model user
      */
    
    public $user_config = [
      'model' => 'UserModel',
      'username_coloumn'=>'email',
      'password_coloumn'=>'password',
      'key_coloumn' => 'apikey',
      'path_coloumn'=>'path',
      'block_coloumn'=>'isblock',
      'whitelist_coloumn'=>'whitelist',
      'blacklist_coloumn'=>'blacklist'
    ];

    /**
     * @var string $headerKey 
     * Header Authentication API KEY
     */

    public $headerKey = 'X-TTPG-KEY';

    public $token_data = 'username';
}