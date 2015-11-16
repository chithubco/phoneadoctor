<?php

namespace frontend\controllers;

use yii\helpers\Url;
use common\models\UploadForm;
use yii\web\UploadedFile;
require_once("common/components/Send.php");
require_once("common/components/checkLogin.php");
class AccountController extends \yii\web\Controller
{
    
    public $layout = "site";

    public function beforeAction($action) { 
    	if(!check())
        	return $this->redirect(Url::toRoute('/site/login'));

    	return true;
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionChangepin()
    {
        $session = \Yii::$app->session;
        $resp='';
        

        if($_POST){
            
            $response = pull('user/api','
                <request method="user.changePin">
                  <user>
                  <old_pin>'.$_POST['pin'].'</old_pin>    
                  <new_pin>'.$_POST['newpin'].'</new_pin>    
                  <confirm_pin>'.$_POST['newpin2'].'</confirm_pin>   
                  <user_id>'.$session['id'].'</user_id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
            
            if($response->body->response_code==100){
            
            
            return $this->redirect(Url::toRoute('/consultation/index'));
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        return $this->render('pin',[
            "error"=>$resp,
            ]);
    }

    public function actionUpdate()
    {
        $session = \Yii::$app->session;
        $resp='';
        $model = new UploadForm();

    
        $response = pull('user/api','
                <request method="user.getuserinfo">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
        $data = $response->body;

        
    

    
        if (\Yii::$app->request->isPost) {
        	
            $response = pull('user/api','
            	<request method="user.update">
				  <user>
				  <userinfo>
				  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>  
				  <fname>'.$_POST['fname'].'</fname>    
				  <lname>'.$_POST['lname'].'</lname>
				  <password>'.$_POST['pin'].'</password>
				  </userinfo>
				  <patients>
	   
				  <email></email>
				  <security_que_value>test</security_que_value>
				  <DOB>123456</DOB>
				  <address>'.$_POST['address'].'</address>    
				  <sex>'.$_POST['sex'].'</sex>
				  </patients>
				  </user>
				</request>
                ');
            $model->file = UploadedFile::getInstance($model, 'file');

        if ($model->validate()) {                
            $model->file->saveAs('pix/' .$session['id']. '.jpg');
        }
            if($response->body->response_code==100){
            
            
            //return $this->redirect(Url::toRoute('/consultation/index'));
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        return $this->render('edit',[
        	"error"=>$resp,
        	"data"=>$data,
            'model' => $model
        	]);
    }

    public function actionMedical()
    {
        $session = \Yii::$app->session;
        $resp='';
        $model = new UploadForm();

    
        $response = pull('user/api','
                <request method="user.getuserinfo">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
        $data = $response->body;

        
    

    
        if (\Yii::$app->request->isPost) {
            
            $response = pull('user/api','
                <request method="user.update">
                  <user>
                  <userinfo>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>  
                  <fname>'.$_POST['fname'].'</fname>    
                  <lname>'.$_POST['lname'].'</lname>
                  <password>'.$_POST['pin'].'</password>
                  </userinfo>
                  <patients>
       
                  <email></email>
                  <security_que_value>test</security_que_value>
                  <DOB>123456</DOB>
                  <address>'.$_POST['address'].'</address>    
                  <sex>'.$_POST['sex'].'</sex>
                  </patients>
                  </user>
                </request>
                ');
            $model->file = UploadedFile::getInstance($model, 'file');

        if ($model->validate()) {                
            $model->file->saveAs('pix/' .$session['id']. '.jpg');
        }
            if($response->body->response_code==100){
            
            
            //return $this->redirect(Url::toRoute('/consultation/index'));
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        return $this->render('medical',[
            "error"=>$resp,
            "data"=>$data,
            'model' => $model
            ]);
    }

    public function actionDetails()
    {
        $session = \Yii::$app->session;
        $details=$session['consult'];
        //var_dump($details);
        
        return $this->render('details',[
        	"details"=>$details
        	]);
    }



}
