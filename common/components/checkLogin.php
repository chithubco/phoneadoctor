<?php


    function check() {
        $session = \Yii::$app->session;
        
        if(!isset($session['id']) || $session['id'] == '')
            return false;
        if(!isset($session['authkey']) || $session['authkey'] == '')
            return false;

        return true;
        //$uri = 'http://localhost/phoneadoc/api/web/index.php/v1/'.$method;
        /*$uri = 'http://localhost/phoneadoctor/index.php/user/api';
        $data = 
        $response = \HttpFull\Request::post($uri)
        ->expectsJson()
        ->body($data)
        ->sendsXml()
        ->send();
        return $response;*/
    }


    

