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
     * @var Array $user_config  
     * Configuration user check
    */
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
    
    

    public $RestUser = [
      [
        'email'=>'user@email.com',
        'password'=>'password',
        'apikey'=>'123123',
        'isblock'=>false,
        'whitelist'=>[],
        'blacklist'=>[],
        'path'=>'*'
      ]
    ];

    /**
     * @var string $headerKey 
     * Header Authentication API KEY
     */

    public $headerKey = 'X-TTPG-KEY';

    public $token_data = 'username';

    public $allowed_format = ['json', 'xml', 'csv'];

    public $default_format = 'json';
}