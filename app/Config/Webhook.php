<?php
namespace Appkita\CI4Restfull\Webhook\Config;

class Webhook
{
    /**
     * for autorun webhook
     */
    public $autorun = true;
    /**
     * Webhook port
     * @param  integer $port
     */
    public $port = 9000;
    /**
     * Header signature
     * @param string $signature
     * empty value for disable;
     */
    public $signature = 'HTTP_X_CS_SIGNATURE';
    /**
     * Header timestamp
     * @param string $timestamp
     * empty value for disable;
     */
    public $timestamp = 'HTTP_X_CS_TIMESTAMP';

    /** @var array $logging
     * you can set file or database
     * if you set database model is model name log class  ex: Model/WebhookLogModel.php
     * if you set file model is folder name log in %WRITEPATH%
     */
    public $logging = [
      'saveto'=>'file',
      'model'=>'webhook'
    ];
}