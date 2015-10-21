<?php

/* 
 * File : Rest.inc.php
 * Author : Arun Kumar Sekar
 */
namespace common\components;
 
use Yii;
use yii\base\Component;

class REST extends Component {

    public $_allow          = array();
    public $_content_type   = "application/xml";
    public $_request        = array();
    
    private $_method        = "";
    private $_code          = 200;

    public function response($data, $status) {
        $this->_code = ($status) ? $status : 200;
        $this->set_headers();
        echo $data;
        exit;
    }

    private function get_status_message() {
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($status[$this->_code]) ? $status[$this->_code] : $status[500];
    }

    public function get_request_method() {
        $this->_method = $_SERVER['REQUEST_METHOD'];
        return $this->_method;
    }
    
    public function get_connection_type() {
        return $_SERVER['SERVER_PORT'];
    }

    private function set_headers() {
        header("HTTP/1.1 " . $this->_code . " " . $this->get_status_message());
        header("Content-Type:" . $this->_content_type);
    }

}

?>