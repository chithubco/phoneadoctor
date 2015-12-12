<?php

namespace frontend\controllers;
use yii\db\Query;
use yii\helpers\Url;
use common\models\UploadForm;
use app\models\PatientAllergies;
use app\models\PatientMedications;
use app\models\PatientActiveProblems;
use app\models\Rxnconso;
use yii\web\UploadedFile;
require_once("common/components/Send.php");
require_once("common/components/checkLogin.php");
class AccountController extends \yii\web\Controller
{
    
    public $layout = "site";
    public $folder = "/home/devphoneadoctorc/public_html/portal/sites/default/patients";

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

    public function actionUpdate() {
        

        $session = \Yii::$app->session;
        $resp = '';
        $model = new UploadForm();
        $success_flag = '';
        
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != NULL) {
            $file = \yii\web\UploadedFile::getInstanceByName('file');
               
            $extension = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.'));               
            if (!is_dir($this->folder . "/" . $session['id'])){
                mkdir($this->folder . "/" . $session['id']);
            }
            $img_url = 'http://' . $_SERVER['SERVER_NAME'] . '/portal/sites/default/patients/' . $session['id'] . "/" . $session['id'] . $extension;                    
            $file->saveAs($this->folder . "/" . $session['id'] . "/" . $session['id'] . $extension);
               
         }

        if (\Yii::$app->request->isPost) {

            $pull_string = '<request method="user.update">
				  <user>
				  <userinfo>
				  <id>' . $session['id'] . '</id>    
                                  <auth_key>' . $session['authkey'] . '</auth_key>  
				  <fname>' . $_POST['fname'] . '</fname>    
				  <lname>' . $_POST['lname'] . '</lname>
				  <password>' . $_POST['password'] . '</password>
				  </userinfo>
				  <patients>
                                  <sex>' . $_POST['sex'] . '</sex>                                  
                                  <DOB>' . date('Y-m-d', strtotime($_POST['DOB'])) . '</DOB>
                                  <marital_status>' . $_POST['marital_status'] . '</marital_status>    
                                  <address>' . $_POST['address'] . '</address>
				  <city>' . $_POST['city'] . '</city>    
				  <state>' . $_POST['state'] . '</state>                                      
                                  <country>' . $_POST['country'] . '</country>
                                  <zipcode>' . $_POST['zipcode'] . '</zipcode>
				  <email>' . $_POST['email'] . '</email>				  
				  <guardians_name>' . $_POST['guardians_name'] . '</guardians_name>	
                                  <skypeid>' . $_POST['skypeid'] . '</skypeid>';     
            
             if(isset($img_url) && $img_url!= NULL){
                $pull_string .='<image>'. $img_url .'</image>';    
             }
             
             $pull_string .='</patients>
				 </user>
                                </request>';
                
            
             $response = pull('user/api', $pull_string);
              
            if ($response->body->response_code == 100) {
                $resp = $response->body->description;
                $success_flag = 1;
            }else{
                $resp = $response->body->description;
                $success_flag = 2;
            }
            $response = pull('user/api', '
            <request method="user.getuserinfo">
              <user>
              <id>' . $session['id'] . '</id>    
              <auth_key>' . $session['authkey'] . '</auth_key>    
              </user>
            </request>
            ');
            $data = $response->body;            

        } else {
            $response = pull('user/api', '
                <request method="user.getuserinfo">
                  <user>
                  <id>' . $session['id'] . '</id>    
                  <auth_key>' . $session['authkey'] . '</auth_key>    
                  </user>
                </request>
                ');
            $data = $response->body;
        }

        return $this->render('edit', [
                    "resp" => $resp,
                    "success_flag" => $success_flag,
                    "data" => $data,
                    'model' => $model
                ]);
    }
    
public function actionDelete_allergy()
    { 
        $session = \Yii::$app->session;
        $resp='';
        

        if(isset($_POST) && $_POST['id']!= NULL){
            
            $response = pull('user/api','
                <request method="user.deleteAllergies">
                  <user>
                  <allergies> 
                  <id>'.$_POST['id'].'</id>
                  </allergies>
                  </user>
                </request>
                ');
            
            if($response->body->response_code==100){
                echo 1;
                exit;
            }else{
                $resp = $response->body->description;
                echo $resp;
                exit;
            }
        }
       return $this->render('medical',[
            ]);
    }    
    
public function actionDelete_medication()
    { 
        $session = \Yii::$app->session;
        $resp='';
        

        if(isset($_POST) && $_POST['id']!= NULL){
            
            $response = pull('user/api','
                <request method="user.deleteMedication">
                  <user>
                  <medication> 
                  <id>'.$_POST['id'].'</id>
                  </medication>
                  </user>
                </request>
                ');
            
            if($response->body->response_code==100){
                echo 1;
                exit;
            }else{
                $resp = $response->body->description;
                echo $resp;
                exit;
            }
        }
       return $this->render('medical',[
            ]);
    }  

public function actionDelete_active_problem()
    { 
        $session = \Yii::$app->session;
        $resp='';
        

        if(isset($_POST) && $_POST['id']!= NULL){
            
            $response = pull('user/api','
                <request method="user.deleteActiveproblems">
                  <user>
                  <activeproblem> 
                  <id>'.$_POST['id'].'</id>
                  </activeproblem>
                  </user>
                </request>
                ');
            
            if($response->body->response_code==100){
                echo 1;
                exit;
            }else{
                $resp = $response->body->description;
                echo $resp;
                exit;
            }
        }
       return $this->render('medical',[
            ]);
    }    
    
public function actionDelete_patient_doc()
    { 
        $session = \Yii::$app->session;
        $resp='';
        

        if(isset($_POST) && $_POST['id']!= NULL){
            
            $response = pull('user/api','
                <request method="user.deletePatientdoc">
                  <user>
                  <patient_doc> 
                  <id>'.$_POST['id'].'</id>
                  </patient_doc>
                  </user>
                </request>
                ');
            
            if($response->body->response_code==100){
                echo 1;
                exit;
            }else{
                $resp = $response->body->description;
                echo $resp;
                exit;
            }
        }
       return $this->render('medical',[
            ]);
    }   
   
    public function actionGetstr(){
        
        $search_val = $_REQUEST['query'];       
        $query = new Query;
        $query->select('STR') 
            ->from('rxnconso')
            ->where('(SAB = "RXNORM") AND STR LIKE "%'.$search_val.'%"')
            ->groupBy('RXCUI')
            ->limit(100);
        
        $command = $query->createCommand();                
        $results = $command->queryAll();    
        foreach ($results as $value) {
            $str_res[] = $value['STR'];
        }
        $str_data = ($str_res!=NULL)?$str_res:"No results found";  
        
        return json_encode($str_data);
    }

    public function actionMedical() {
        $session = \Yii::$app->session;
        $resp = '';
        $model = new PatientMedications;
        $success_flag = '';

        if (\Yii::$app->request->isPost) {//echo "<pre>";print_r($_FILES);exit;
            
            if (isset($_POST['patient_doc']) && $_POST['patient_doc'] != NULL) {
                //Document Upload
                $response = pull('document/api', '
                <request method="document.create">
                  <user>
                  <id>' . $session['id'] . '</id>    
                  <auth_key>' . $session['authkey'] . '</auth_key>    
                  </user>
                  <document>
                  <name>' . $_FILES['doc']['name'] . '</name>
                  <type>' . $_FILES['doc']['type'] . '</type>
                  <tmp_name>' . $_FILES['doc']['tmp_name'] . '</tmp_name>
                  <error>' . $_FILES['doc']['error'] . '</error>
                  <size>' . $_FILES['doc']['size'] . '</size>
                  </document>
                </request>');
                
            } else {
                //Text Data
                $pull_string = '<request method="user.addmedical">
                  <user>
                  <password>' . $session['authkey'] . '</password>
                <userinfo>
                <id>' . $session['id'] . '</id>    
                <auth_key>' . $session['authkey'] . '</auth_key>  
                </userinfo>';

                //Patient Allergies
                if (isset($_POST['allergies']) && $_POST['allergies'] != NULL) {
                    
                    $pull_string .= '<alergies>';
                    if (isset($_POST['allergyid']) && $_POST['allergyid'] != NULL) {
                        $pull_string .= '<id>' . $_POST['allergyid'] . '</id>';
                    }
                    $pull_string .= '<allergy_type>' . $_POST['allergy_type'] . '</allergy_type>
                                <allergy>' . $_POST['allergy'] . '</allergy>               
                                <reaction>' . $_POST['reaction'] . '</reaction>
                                <severity>' . $_POST['severity'] . '</severity>
                                <location>' . $_POST['location'] . '</location>
                                <begin_date>' . date('Y-m-d', strtotime($_POST['begin_date'])) . '</begin_date>
                                <end_date>' . date('Y-m-d', strtotime($_POST['end_date'])) . '</end_date>
                                </alergies>';
                }
                
                //Patient Medications
                if (isset($_POST['medications']) && $_POST['medications'] != NULL) {
                    
                    $pull_string .= '<medications>';
                    if (isset($_POST['medicationid']) && $_POST['medicationid'] != NULL) {
                        $pull_string .= '<id>' . $_POST['medicationid'] . '</id>';
                    }
                    $pull_string .= '<eid>0</eid>    
                                <STR>' . $_POST['STR'] . '</STR>
                                <form>' . $_POST['form'] . '</form>
                                <route>' . $_POST['route'] . '</route>
                                <dose>' . $_POST['dose'] . '</dose>
                                <begin_date>' . date('Y-m-d', strtotime($_POST['begin_date'])) . '</begin_date>
                                <end_date>' . date('Y-m-d', strtotime($_POST['end_date'])) . '</end_date>                    
                                </medications>';
                }
                
                //Active Problems
                if (isset($_POST['activeproblems']) && $_POST['activeproblems'] != NULL) {
                    
                    $pull_string .= '<active_problems>';
                    if (isset($_POST['problemid']) && $_POST['problemid'] != NULL) {
                        $pull_string .= '<id>' . $_POST['problemid'] . '</id>';
                    }
                    
                    $pull_string .= '<eid>0</eid>     
                                <code_type>' . $_POST['code_type'] . '</code_type>
                                <referred_by>' . $_POST['referred_by'] . '</referred_by>                                    
                                <code_text>' . $_POST['code_text'] . '</code_text>
                                <occurrence>' . $_POST['occurrence'] . '</occurrence>
                                <outcome>' . $_POST['outcome'] . '</outcome>               
                                <begin_date>' . date('Y-m-d', strtotime($_POST['begin_date'])) . '</begin_date>
                                <end_date>' . date('Y-m-d', strtotime($_POST['end_date'])) . '</end_date>                    
                                </active_problems>';
                }
                

                $pull_string .= ' </user>
                </request>';

                $response = pull('user/api', $pull_string);
            }

            if ($response->body->response_code == 100) {
                $resp = $response->body->description;
                $success_flag = 1;
            }else{
                $resp = $response->body->description;
                $success_flag = 2;
            }
            
            $response1 = $this->getMedicalHistory('user.getPatientAllergies',$session['id'],$session['authkey']);
            $patient_allergies = $response1->body->description;
            
            $response2 = $this->getMedicalHistory('user.getPatientMedications',$session['id'],$session['authkey']);
            $patient_medications = $response2->body->description;
            
            $response3 = $this->getMedicalHistory('user.getPatientActiveProblems',$session['id'],$session['authkey']);
            $active_probs = $response3->body->description;
            
            $response4 = $this->getMedicalHistory('user.getPatientDocs',$session['id'],$session['authkey']); 
            $patient_docs = $response4->body->description; 
            
        } else {
         
            $response1 = $this->getMedicalHistory('user.getPatientAllergies',$session['id'],$session['authkey']);
            $patient_allergies = $response1->body->description;
            
            $response2 = $this->getMedicalHistory('user.getPatientMedications',$session['id'],$session['authkey']);
            $patient_medications = $response2->body->description;
            
            $response3 = $this->getMedicalHistory('user.getPatientActiveProblems',$session['id'],$session['authkey']);
            $active_probs = $response3->body->description;
            
            $response4 = $this->getMedicalHistory('user.getPatientDocs',$session['id'],$session['authkey']); 
            $patient_docs = $response4->body->description;            
        }
        //echo "<pre>";print_r($patient_docs);exit;
        return $this->render('medical', [
                    "resp" => $resp,
                    "success_flag" => $success_flag,
           
                    "patient_allergies" => $patient_allergies,
                    "patient_medications" => $patient_medications,
                    "active_problems" => $active_probs,
                    "patient_docs"=>$patient_docs,
                    'model' => $model
                ]);
    }
    
    public function getMedicalHistory($category,$userId,$authkey){
        
          $response = pull('user/api', '
                <request method="'.$category.'">
                  <user>
                  <id>' . $userId . '</id>    
                  <auth_key>' . $authkey . '</auth_key>    
                  </user>
                </request>
                ');
        return $response;
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
