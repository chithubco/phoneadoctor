<?php


    function pull($method,$data) {
        //$uri = 'http://phoneadoctor.com.ng/api/web/index.php/v1/'.$method;
        $uri = 'http://localhost/phoneadoc/api/web/index.php/v1/'.$method;
        $response = \HttpFull\Request::post($uri)
        ->expectsJson()
        ->body($data)
        ->sendsXml()
        ->send();
        //var_dump($response->body);
        //exit;
        return $response;
    }


    

