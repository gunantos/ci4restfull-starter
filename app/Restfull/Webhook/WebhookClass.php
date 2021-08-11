<?php

class WebhookClass {
    protected $config = null;

    function __construct() {
       $path_config = \dirname(\dirname(\dirname(__FILE__))).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Config';
       $config_file = $path_config .DIRECTORY_SEPARATOR.'Webhook.php';
       if (!\file_exists($config_file)) {
            throw new Exception('Configuration file not found');
       }
       require_once $config_file;
       $this->config = new \Appkita\CI4Restfull\Webhook\Config\Webhook();
    }

    public function build(int $port) {
        $secret = 'your-env-secret';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $receivedHmac = $_SERVER[strtoupper($this->config->signature)] ?? '';
        $timestamp = $_SERVER[strtoupper($this->config->timestamp)] ?? '';
        $url = $_SERVER['REQUEST_URI'];
        $body = file_get_contents('php://input');

        $data = $requestMethod . $url . $timestamp . $body;
        $hmac = hash_hmac('sha256', $data, $secret);

        $isValid = hash_equals($hmac, $receivedHmac);

        if (!$isValid) {
            die();
        }

        $webhook = json_decode($body);
        file_put_contents('php://stdout', 'Webhook event received: ' . print_r($webhook, true) . "\r\n");
    }
}