<?php


    function check() {
        $session = \Yii::$app->session;
        
        if(!isset($session['id']) || $session['id'] == '')
            return false;
        if(!isset($session['authkey']) || $session['authkey'] == '')
            return false;

        //$uri = 'http://phoneadoctor.com.ng/api/web/index.php/v1/user/api';
        $uri = 'http://localhost/phoneadoc/api/web/index.php/v1/user/api';
        $data = '
                <request method="user.getuserinfo">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ';
        
        $response = \HttpFull\Request::post($uri)
        //->expectsJson()
        ->body($data)
        ->sendsXml()
        ->send();
        
        $response_des = json_decode($response->body);        
        $session['fname'] = $response_des->description->fname;
        $session['lname'] = $response_des->description->lname;

        //var_dump($response->body);
       // exit;
         if($response_des->response_code==100)
            return true;
        else
            return false;

    }


    

