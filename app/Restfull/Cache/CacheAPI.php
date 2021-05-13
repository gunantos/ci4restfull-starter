<?php
namespace Appkita\CI4Restfull\Cache;

class CacheAPI extends \CodeIgniter\Entity
{
    protected $attributes = [
        'ipaddress'=>null,
        'controller'=>null,
        'function'=>null,
        'auth'=>null,
        'format'=>null,
        'request'=>null,
        'header'=>null,
        'respond'=>null,
        'start_time'=>null,
        'end_time'=>null,
        'user'=>null
    ];

}