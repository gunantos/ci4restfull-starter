<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Restfull extends BaseConfig
{
    /**
     * @var String $cekfrom | database or file
     */
    public $cekfrom = 'file';
    
    /**
     * @var Array $user_config  
     * Configuration user check
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
     * @var array $UserModel
     * list user api if you using file configuration 
     */
    public $UserModel = [
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

    public $headerKey = 'X-API-KEY';

    /**
     * @var array $allowed_key_parameter
     * if you API KEY allowed get from parameter GET, POST, or JSON
     */
    public $allowed_key_parameter = ['get', 'post', 'json'];

    /**
     * @var string $token_data
     * Data include to token
     */
    public $token_data = 'username';

    /**
     * @var Array $allowed_format
     * allowe format output
     */
    public $allowed_format = ['json', 'xml', 'csv'];

    /**
     * @var string $default_format
     */
    public $default_format = 'json';

    /**
     * @var array $logging
     * you can set file or database
     */
    public $logging = [
      'saveto'=>'file',
      'model'=>'restfull'
    ];

    public $default_message_error = [
      '400' => 'Bad Request',
      '401' => 'Not Authentication',
      '402' => 'Payment Required',
      '403' => 'You request is Forbidden',
      '404' => 'URL Not Found',
      '405' => 'Method Not Allowed by system',
      '406' => 'You request not Acceptable',
      '408' => 'Request Timeout',
      '500' => 'Internal Server Error',
      '501' => 'Not Implemented',
      '502' => 'Bad Gateway',
      '503' => 'Service Unavailable',
      '504' => 'Gateway Timeout',
      '505' => 'HTTP Version not Supporterd',
      '511' => 'Network Authentication Required'
    ];
}