<?php

/* 
 * File : userAuthentication 
 */
namespace common\components;
 
use Yii;
use yii\base\Component;
use app\models\User;

class userAuthentication extends Component {

    public $userId         = 0;
    public $auth_key       = 0;
    
    //public function checkAuthKey($userId, $authKey) {
    public function checkAuthKey() {

       //Authenticate auth key
       $auth_key_exists = User::find()->where('id = ' . $this->userId . ' AND auth_key LIKE "' . $this->auth_key . '"')->one();
       return ($auth_key_exists)?true:false;
    }    


}

?>