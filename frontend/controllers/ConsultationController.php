<?php

namespace frontend\controllers;

use yii\helpers\Url;
require_once("common/components/Send.php");
require_once("common/components/checkLogin.php");
class ConsultationController extends \yii\web\Controller
{
    
    public $layout = "site";
    public $close_time = "16:55:00";

    public function beforeAction($action) { 
    	if(!check())
        	return $this->redirect(Url::toRoute('/site/login'));

    	return true;
    }
    public function actionIndex()
    { 
        if($_POST){
            $this->actionCreate();
            exit;
        }
        if(date('N', time()) >= 6){
            return $this->redirect('create');
        }
        if(time()>strtotime(date("Y-m-d")." ".$this->close_time)){
            return $this->redirect('create');
        }
        $session = \Yii::$app->session;
        $q = pull('consultation/api','
               <request method="consultation.getslots">
          <consultation>
                  <user_id>'.$session['id'].'</user_id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
          </consultation>
        </request>
                ');
        $slots = NULL;
        //var_dump($q->body);
        if($q->body->response_code==100){
          $slots = $q->body->data;
        }
        return $this->render('index',[
            'slots'=>$slots,
            ]);
    }

    public function actionCreate()
    {
        if(date('N', strtotime($date)) >= 6){
            return $this->render('weekend');
        }
        if(time()>strtotime(date("Y-m-d")." ".$this->close_time)){
            return $this->render('closed');
        }

        $session = \Yii::$app->session;
        $resp='';
        $details=$session['consult'];
        if($details && (strtotime($details->end) > time()))
            return $this->redirect('details');

        $q = pull('consultation/api','
               <request method="consultation.getslots">
          <consultation>
                  <user_id>'.$session['id'].'</user_id>    
                  <auth_key>'.$session['authkey'].'</auth_key>    
          </consultation>
        </request>
                ');
        $slots = NULL;
        //var_dump($q->body);
        if($q->body->response_code==100){
          $slots = $q->body->data;
          if($slots=='' || $slots==NULL || empty($slots)){
            return $this->render('booked');
            }   
        }



        if($_POST){
            if($_POST['agree']!=1){
            return $this->render('create', [
            'error'=>"Please agree to the terms to continue"
        ]);
          }
            $response = pull('consultation/api','
            	<request method="consultation.create">
				  <consultation>
				  <note>'.$_POST['question'].'</note>  
                  <status>'.$_POST['status'].'</status>  
                  <slot>'.strtotime($_POST['slot']).'</slot>  
                  <user_id>'.$session['id'].'</user_id>    
                  <auth_key>'.$session['authkey'].'</auth_key>       
				  </consultation>
				</request>
                ');
            //var_dump($response->body);
            if($response->body->response_code==100){
            $session->set('consult',$response->body->data);
            
            return $this->redirect('details');
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        return $this->render('create',[
        	"error"=>$resp,
            'slots'=>$slots,
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

    public function actionGetprescription() {
        $session = \Yii::$app->session;
        $resp = '';

        if (isset($_POST) && $_POST['id'] != NULL) {

            $query = new Query;
            $query->select('STR')
                    ->from('patient_medications')
                    ->where('eid =' . $_POST['id']);

            $prescriptions = '';
            $command = $query->createCommand();
            $results = $command->queryAll();


            if ($results != NULL && is_array($results)) {
                $i = 1;
                foreach ($results as $value) {
                    $prescriptions .= "<li>" . $i . ". " . $value['STR'] . "</li>";
                    $i++;
                }
            } else {
                $prescriptions = "<li>No prescriptions found.</li>";
            }
        }
        echo $prescriptions;
        exit;

        return $this->render('history', []);
                
    }
    
    public function actionGetpatientnotes(){
        $session = \Yii::$app->session;
        $resp = '';

        if (isset($_POST) && $_POST['id'] != NULL) {

            $query = new Query;
            $query->select('body')
                    ->from('patient_notes')
                    ->where('eid =' . $_POST['id']);

            $descriptions = '';
            $command = $query->createCommand();
            $results = $command->queryAll();


            if ($results != NULL && is_array($results)) {
                $i = 1;
                foreach ($results as $value) {
                    $descriptions .= "<li>" . $i . ". " . $value['body'] . "</li>";
                    $i++;
                }
            } else {
                $descriptions = "<li>No descriptions found.</li>";
            }
        }
        echo $descriptions;
        exit;

        return $this->render('history', []);
        
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
