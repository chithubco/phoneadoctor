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
                                  <skypeid>'.$_POST['skypeid'].'</skypeid>
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

    
        if (\Yii::$app->request->isPost) {//echo "<pre>";print_r($_POST);exit;
            
            $response = pull('user/api','
                <request method="user.addmedical">
                  <user>
                  <password>'.$session['authkey'].'</password>
                <userinfo>
                <id>'.$session['id'].'</id>    
                <auth_key>'.$session['authkey'].'</auth_key>  
                </userinfo>
                  
                <alergies>
                <allergy_type>'.$_POST['allergy_type'].'</allergy_type>
                <allergy>'.$_POST['allergy'].'</allergy>
               
                <reaction>'.$_POST['reaction'].'</reaction>
                <severity>'.$_POST['severity'].'</severity>
                <location>'.$_POST['location'].'</location>
                </alergies>
                
                <medications>
                <STR>'.$_POST['STR'].' STR</STR>    
                <RXCUI>'.$_POST['RXCUI'].'</RXCUI>
                <CODE>'.$_POST['CODE'].'</CODE>
                <ICDS>'.$_POST['ICDS'].'</ICDS>

                <ocurrence>'.$_POST['ocurrence'].'</ocurrence>
                <form>'.$_POST['form'].'</form>
                <route>'.$_POST['route'].'</route>
                </medications>
                
                <active_problems>
                <code_text>'.$_POST['code_text'].'</code_text>    
              
                <occurrence>'.$_POST['ap_ocurrence'].'</occurrence>
                <outcome>'.$_POST['outcome'].'</outcome>
                <referred_by>'.$_POST['referred_by'].'</referred_by>
                </active_problems>

                  </user>
                </request>
                ');
            
            $model->file = UploadedFile::getInstance($model, 'file');

        /*if ($model->validate()) {                
            $model->file->saveAs('pix/' .$session['id']. '.jpg');
        }*/
            if($response->body->response_code==100){            
            
            return $this->redirect(Url::toRoute('/consultation/index'));
            }
            $resp = $response->body->description;
            $data = $_POST;
            
        }else{
        $data ='';    
        $response = pull('user/api','
                <request method="user.getPatientAllergies">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
        $patient_allergies = $response->body->description; 

        $response = pull('user/api','
                <request method="user.getPatientMedications">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
        $patient_medications = $response->body->description; 
            
        $response = pull('user/api','
                <request method="user.getPatientActiveProblems">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
                  </user>
                </request>
                ');
        $active_probs = $response->body->description; 
        
        }
        //echo "<pre>";print_r($data);exit;
        return $this->render('medical',[
            "error"=>$resp,
            "data"=>$data,
            "patient_allergies"=>$patient_allergies,
            "patient_medications"=>$patient_medications,
            "active_problems"=>$active_probs,
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
