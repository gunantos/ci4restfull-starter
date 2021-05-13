<?php
namespace Appkita\CI4Restfull\Cache;
use \CodeIgniter\Entity;
class CacheUSER extends Entity
{
    protected $attributes = ['auth'];

    function __construct(array $config) {
        $this->createAttributes($config);
        parent::__construct();
    }
    
    public function createAttributes(array $config) {
        $allowed_format  = [
            'username_coloumn',
            'password_coloumn',
            'key_coloumn',
            'path_coloumn',
            'block_coloumn',
            'whitelist_coloumn',
            'blacklist_coloumn'
        ];
        if (\is_array($config)) {
            foreach ($config as $key => $value) {
                if (\in_array($key, $allowed_format)) {
                    $this->attributes[$value] = null;
                }
            }
        }
    }

}