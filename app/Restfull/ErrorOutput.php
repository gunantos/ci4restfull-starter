<?php
namespace Appkita\CI4Restfull;

class ErrorOutput
{
    private static function getFormat(string $default) : string {
        if (isset($_GET['format']) && !empty($_GET['format'])){
            return $_GET['format'];
        } else if (isset($_POST['format']) && !empty($_POST['format'])) {
            return $_POST['format'];
        } else{
            $json = \file_get_contents('php://input');
            $data = json_decode($json);
            if (isset($data->format) && !empty($data->format)) {
                return $data->format;
            }
        }
        return $default;
    }

    private static function init(int $code = 404, $description = null) {
        $config = new \Config\Restfull();
        $allowed = $config->allowed_format;
        $default_format = $config->default_format;
        $format = strtolower(str_replace(' ', '', ErrorOutput::getFormat($default_format)));
        if (!\in_array($format, $allowed)) {
            $format = $default_format;
        }
        $message = $config->default_message_error;
        if (empty($description)) {
            if (isset($message[$code])) {
                $description = $message[$code];
            }
        }
        return (object) [
            'output'=>['status'=>$code, 'description'=>$description],
            'format'=>$format
        ];
    }

    private static function setHeader(string $format) {
        switch(\strtolower(\str_replace($format))) {
            case 'json':
                return 'Content-type: application/json';
                break;
            case 'xml':
                break;
            case 'csv':
                break;
        }
    }

    public static function output(int $code, $description = null) {
        $init = ErrorOutput::init($code, $description);
       $format = strtolower(str_replace(' ', '', $init->format));
       if (!\in_array($format, ['json', 'xml'])) {
            $format = 'json';
        }
        $mime   = "application/{$format}";
        \header('Content-type: '. $mime);
        die(json_encode($init->output));
        exit();
    }

    public static function error404(String $description = null) {
        return ErrorOutput::output(404, $description);
    }

    public static function error401(String $description = null) {
        return ErrorOutput::output(401, $description);
    }
}