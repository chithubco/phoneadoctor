<?php


    function check() {
        $session = \Yii::$app->session;
        
        if(!isset($session['id']) || $session['id'] == '')
            return false;
        if(!isset($session['authkey']) || $session['authkey'] == '')
            return false;

        //$uri = 'http://localhost/phoneadoc/api/web/index.php/v1/'.$method;
        $uri = 'http://phoneadoctor.com.ng/app/api/web/index.php/v1/user/api';
        $data = '
                <request method="user.getuserinfo">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ';
        
        $response = \HttpFull\Request::post($uri)
        ->expectsJson()
        ->body($data)
        ->sendsXml()
        ->send();
        //var_dump($response->body);
       // exit;
        if($response->body->response_code==100)
            return true;
        else
            return false;

    }


    

