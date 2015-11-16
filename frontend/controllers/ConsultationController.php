<?php

namespace frontend\controllers;

use yii\helpers\Url;
require_once("common/components/Send.php");
require_once("common/components/checkLogin.php");
class ConsultationController extends \yii\web\Controller
{
    
    public $layout = "site";

    public function beforeAction($action) { 
    	if(!check())
        	return $this->redirect(Url::toRoute('/site/login'));

    	return true;
    }
    public function actionIndex()
    {
        if($_POST){
            $this->actionCreate();
        }
        return $this->render('index');
    }

    public function actionCreate()
    {
        $session = \Yii::$app->session;
        $resp='';
        $details=$session['consult'];
        if($details && (strtotime($details->end) > time()))
            return $this->redirect('details');
        if($_POST){
            
            $response = pull('consultation/api','
            	<request method="consultation.create">
				  <consultation>
				  <note>'.$_POST['question'].'</note>  
                  <user_id>'.$session['id'].'</user_id>    
                  <auth_key>'.$session['authkey'].'</auth_key>       
				  </consultation>
				</request>
                ');
            
            if($response->body->response_code==100){
            $session->set('consult',$response->body->data);
            
            return $this->redirect('details');
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        return $this->render('create',[
        	"error"=>$resp
        	]);
    }

    public function actionHistory()
    {
        $session = \Yii::$app->session;
        $resp='';
        
            
            $response = pull('consultation/api','
                <request method="consultation.getConsultationHistory">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <auth_key>'.$session['authkey'].'</auth_key>   
                  </user>
                </request>
                
                ');
            
            //var_dump($response->body);
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        
        return $this->render('history',[
            "data"=>$resp
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
