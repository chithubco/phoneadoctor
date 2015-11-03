<?php


    function pull($method,$data) {
        //$uri = 'http://localhost/phoneadoc/api/web/index.php/v1/'.$method;
        $uri = 'http://localhost/phoneadoctor/index.php/'.$method;
        $response = \HttpFull\Request::post($uri)
        ->expectsJson()
        ->body($data)
        ->sendsXml()
        ->send();
        return $response;
    }


    

