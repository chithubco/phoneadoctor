<?php

namespace app\controllers;
namespace frontend\controllers;


use Yii;
use common\models\Patient;
use common\models\Users;
use app\models\Settings;
use app\models\ApiLog;
use common\models\CalendarEvents;
use common\models\Consultations;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;

include_once("common/components/xmlToArray.php");
include_once("common/components/XmlDomConstruct.php");

class ConsultationController extends Controller
{
    public $administrator_email;
    public $base_currency_code;
    public $configURL;
    public $homeURL;
    public $salt                = 'phoneDoctor';
    public $currencyLabel       = 'N';  
    public $twilio_from_phone;
    public $twilio_account_sid;
    public $twilio_auth_token;
    
    
   public function beforeAction($action) { 
        

        $settingValues = Settings::find()->all();                        


        //$settingValues = Settings::model()->findAll();
        foreach($settingValues as $setting) {
            switch($setting->name) {  
                
                case 'administrator_email':
                    $this->administrator_email = $setting->value;
                    break;                
                   
                case 'base_currency_code':
                    $this->base_currency_code = $setting->value;
                    break;

                case 'configURL':
                    $this->configURL = $setting->value;
                    break;
                
                case 'twilio_auth_token':
                    $this->twilio_auth_token = $setting->value;
                    break;
                
                case 'twilio_account_sid':
                    $this->twilio_account_sid = $setting->value;
                    break;
                
                case 'twilio_from_phone':
                    $this->twilio_from_phone = $setting->value;
                    break;  
                
                default:
                    break;
            }
        }        
        //set the home URL
        $this->homeURL = 'http://' . $_SERVER['SERVER_NAME'].'phoneadoctor';
        return true;
    }    
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    // User APIS
    public function actionApi(){
        
        $requestMethod =  Yii::$app->REST->get_request_method();
        $connectionType = Yii::$app->REST->get_connection_type();                
       
        //use php://input to get the raw $_POST results
        $xmlInput = file_get_contents('php://input');

        //parse the incoming XML to array
        $xmlArray = xml2array($xmlInput);
        //echo'<pre>';print_r($xmlArray);echo'</pre>';

        if (!isset($xmlArray['request']) || !isset($xmlArray['request_attr'])) {
            $this->generateJsonResponce(array("response_code" => 112, "description" => 'Invalid request, please check your input.'), 'error', 400);
        } else if ($requestMethod <> 'POST') {
            $this->generateJsonResponce(array("response_code" => 101, "description" => 'Only POST method supported.'), 'error', 403);
        } /*else if ($connectionType <> 443) {
            $this->generateJsonResponce(array("response_code" => 111, "description" => 'Insecure connection.'), 'error', 406);
        }*/ else {  
            //determine the method
            switch ($xmlArray['request_attr']['method']) {
                case 'consultation.create':
                    $this->createConsultation($xmlArray['request']);
                    break;
                case 'consultation.update':
                    $this->updateConsultation($xmlArray['request']);
                    break;                  
                default:
                   $this->generateJsonResponce(array("response_code" => 999, "description" => 'Unknown method.'), 'error', 400);
                    break;
            }
        }
    }

    

    
   

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CalendarEvents::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    protected function findDoctors()
    {
        $model = Users::findAll("role_id = 2 AND online = 1");
        return $model;
        
    }



    protected function findfreeDoctor()
    {
                //Check for doctor with empty calendar
                $command = Yii::$app->db->createCommand("SELECT users.id FROM users LEFT OUTER JOIN calendar_events ON 
                    users.id=calendar_events.user_id WHERE calendar_events.user_id IS NULL AND users.role_id = 2 AND users.online = 1");
                $result1= $command->queryAll();
                
                //if no doctor has empty calendar, look for one with the lest amount of schedule
                if(empty($result1[0])){
                $command = Yii::$app->db->createCommand("SELECT MIN(cnt) as cnt FROM (SELECT COUNT(*) cnt FROM calendar_events GROUP BY user_id) t;");
                $result= $command->queryAll();

                $command = Yii::$app->db->createCommand("SELECT * FROM calendar_events t1
                            JOIN (SELECT user_id FROM calendar_events GROUP BY user_id HAVING COUNT(*) = {$result[0]['cnt']}) t2
                            ON t1.user_id = t2.user_id;");
                $result2= $command->queryAll();
                return $result2[0]['user_id'];
                
                }else{
                return $result1[0]['id'];
                }
        
    }

    protected function getSlot($doc)
    {
        //get doctor's calendar for the day
            $calendar = CalendarEvents::find()->where("user_id = '$doc' and `start` >  '".date("Y-m-d H:i:s")."'
            AND  `end` <  '".date("Y-m-d")."  23:59:00'")->one();
            return $calendar;
        //
        
    }

    protected function getNearestSlot(){

        $current_time = time();

        $frac = 900;
        $r = $current_time % $frac;
        
        $new_time = $current_time + ($frac-$r);
        $new_date = date('Y-m-d H:i:s', $new_time);
        return $new_date;
    }
   
     /*
     * API Method : user.create
     * Purpose    : Create a user
     * Returns    : Result of insert operation
     */
       public function createConsultation($xmlconsultationDetails) {

      //check the mandatory fields
 
        if (!isset($xmlconsultationDetails['consultation']['note']) || trim($xmlconsultationDetails['consultation']['note']) == '') {
            
            $this->addLogEntry('consultation.create', 'Failure', 9, 'Consultation details missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Consultation details missing.'), 'error', 400);
            
        }else if (!isset($xmlconsultationDetails['consultation']['user_id']) || trim($xmlconsultationDetails['consultation']['user_id']) == '') {
            
            $this->addLogEntry('consultation.create', 'Failure', 9, 'User missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User missing.'), 'error', 400);
            
        } else {
            $note      = $this->sanitizeXML($xmlconsultationDetails['consultation']['note']);
            $user_id  = $this->sanitizeXML($xmlconsultationDetails['consultation']['user_id']);
                
                
                //create a new Consultation
                $model = new CalendarEvents();
                $cons = new Consultations();
                $user = Patient::find()->where("pubpid = '$user_id'")->one();
               
                $name = $user->fname." ".$user->mname." ".$user->lname;
                //Select a doctor
                $doc = $this->findfreeDoctor();
                $calendar = $this->getSlot($doc);
                if($calendar == NULL){
                    //$current_date = date('d-M-Y g:i:s A');
                
                    $new_date = $this->getNearestSlot();
                    $start = $new_date;
                    $end = date("Y-m-d H:i:s",(strtotime($new_date) + (60*15)));
                }else{
                    $start = $calendar->end;
                    $end = date("Y-m-d H:i:s",(strtotime($calendar->end) + (60*15)));
                }

                //$activate_key               = time() . rand(1000, 9999);echo 1;
                $model->notes               = $note;
                $model->user_id             = $doc;
                $model->patient_id           = $user->pid;
                $model->title               = $name;
                $model->category            = 2;
                $model->facility            = 1;
                $model->billing_facillity   = 1;
                $model->status              = '*';
                $model->start            = $start;
                $model->end   = $end;
                
   
                $model->save(false);

                $cons->details               = $note;
                $cons->user_id             = $user_id;
                $cons->save(false);
               
                $this->addLogEntry('consultation.create', 'Success', 3, 'consultation successfully created. Username :- ' . $name, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Consultation successfully created.'), 'ok', 200);               
                exit;
            
         }
       }
       
    /*
     * API Method : user.update
     * Purpose    : Update existing user
     * Returns    : Result of update operation
     */
    
    public function updateConsultation($xmlUserDetails) {
        
        if (!isset($xmlUserDetails['user']['password']) || trim($xmlUserDetails['user']['password']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'Password missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Password missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['firstname']) || trim($xmlUserDetails['user']['firstname']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'Firstname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Firstname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['lastname']) || trim($xmlUserDetails['user']['lastname']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'Lastname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Lastname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'Phone missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone missing.'), 'error', 400);
            
        } 
        else if (!isset($xmlUserDetails['user']['email']) || trim($xmlUserDetails['user']['email']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'User email missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User email missing.'), 'error', 400);
            
        } else if((!isset($xmlUserDetails['user']['birth_date']) || trim($xmlUserDetails['user']['birth_date']) == '')) {
                
            $this->addLogEntry('user.update', 'Failure', 9, 'Date of birth missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Date of birth missing.'), 'error', 400);

        } else if((!isset($xmlUserDetails['user']['location']) || trim($xmlUserDetails['user']['location']) == '')) {
                
            $this->addLogEntry('user.update', 'Failure', 9, 'Location missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Location missing.'), 'error', 400);

        } else {
            
            $model          = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['id']));            
            $userFullName   = $this->sanitizeXML($xmlUserDetails['user']['firstname']).$this->sanitizeXML($xmlUserDetails['user']['lastname']);
            $userPhone      = $this->sanitizeXML($xmlUserDetails['user']['phone']);
            $userFirstName  = $this->sanitizeXML($xmlUserDetails['user']['firstname']);
            $userLastName   = $this->sanitizeXML($xmlUserDetails['user']['lastname']);

            if(!isset($xmlUserDetails['user']['username']) || trim($xmlUserDetails['user']['username']) == '') {
                $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
            } else {
                $userName = $xmlUserDetails['user']['username'];
            }   
            
            

            //check if username is available
            $userFlag = true;
            $user = User::find()
                    ->where('username = "'.$userName.'"')
                    ->all();  
            $userNameUpdateFlag = count($user) >= 2 ? true : false;
            
            if($userNameUpdateFlag){
                while($userFlag){
                    $userName .= rand(100, 999);
                    $userFlag = $this->checkIfUserExists($userName);
                }
            }
            
            //check if email is already in use.
            $email = $this->sanitizeXML($xmlUserDetails['user']['email'], true);
            
            $email_exists = User::find()->where('email LIKE "'.$email.'"')->all();  
            $email_check_flag = count($email_exists) >= 2 ? true : false;
            
            //check if phone no. is already in use
            $phone_exists = User::find()->where('phone LIKE "'.$userPhone.'"')->all();  
            $phone_check_flag = count($email_exists) >= 2 ? true : false;
            
            if(preg_match('/[^A-Za-z0-9]/', $userName)) {
                
                $this->addLogEntry('user.update', 'Failure', 9, 'Illegal characters in username.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                
            } else if(!$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['email'], true))) {
                
                $this->addLogEntry('user.update', 'Failure', 9, 'Invalid email.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                
            } else if($email_check_flag) {
                
                $this->addLogEntry('user.update', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['email']) . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                
            } else if($phone_check_flag) {
                
                $this->addLogEntry('user.update', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                
            } else {
            
                $model->username            = $userName;
                $model->password            = md5($xmlUserDetails['user']['password']);
                $model->phone               = $userPhone;
                $model->firstname           = $userFirstName;
                $model->lastname            = $userLastName;
                $model->email               = $email;
                $model->birth_date          = $this->sanitizeXML($xmlUserDetails['user']['birth_date'], true);
                $model->location            = $this->sanitizeXML($xmlUserDetails['user']['location'], true);
                $model->createtime          = (int) time();
                $model->status              = 1;
                $model->role                = 'USER';
   
                $model->save();
               
                $this->addLogEntry('user.update', 'Success', 3, 'User info successfully updated. Username :- ' . $model->username, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User info successfully updated.'), 'ok', 200);               
                exit;
            }
         }
    }       

   public function addLogEntry($api_method, $type, $log_description, $notes = '', $user_id = 0, $trans_id = 0) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }        
       
        /*$t      = microtime(true);
        $micro  = sprintf("%06d",($t - floor($t)) * 1000000);         
        $d      = new \DateTime( date('Y-m-d H:i:s.' . $micro, $t) );       
        $date   = $d->format("Y-m-d H:i:s.u");*/
        $date   = date("Y-m-d H:i:s");
        
        $api_log = new ApiLog;
        $api_log->api_method_id             = 1;//$this->api_methods[$api_method];
        $api_log->type                      = $type;
        $api_log->api_log_description_id    = $log_description;
        $api_log->notes                     = $notes;
        $api_log->user_id                   = $user_id;
        $api_log->created                   = $date;
        $api_log->device_ip_address         = $remote_ip;
        $api_log->trans_id                  = $trans_id;
        
        $api_log->save(false);
        return true;
    }       
       
 public function generateXMLResponse($response, $status, $http_status = 200, $apiVersion = 'apiv2') {
        //$checkMethod = new REST();

        //$dom = new XmlDomConstructCustomizedForAPI('1.0', 'utf-8');
        Yii::$app->xmlDom->parseMixed(array("response" => $response));
        $data = Yii::$app->xmlDom->saveXML();

        $data = str_replace('<response/>', '<response xmlns="http://' . $_SERVER['SERVER_NAME'] . '/pos/index.php/' . $apiVersion . '" status="' . $status . '" />', $data);
        $data = str_replace('<response>', '<response xmlns="http://' . $_SERVER['SERVER_NAME'] . '/pos/index.php/' . $apiVersion . '" status="' . $status . '" >', $data);
        Yii::$app->REST->response($data, $http_status);
        
        exit;
    }
    
 
public function generateJsonResponce($response){
    
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
    print_r(json_encode($response));
    exit;
}

   /*
     * API Method : N.A
     * Purpose    : Filter out unwanted characters
     * Returns    : Sanitized data
     */

    public function sanitizeXML($clear = '', $excludeSpecialChars = false, $skipURLDecode = false) {
       
        if(trim($clear) <> ''){
            // Strip HTML Tags
            $clear = strip_tags($clear);
            // Clean up things like &amp;
            $clear = html_entity_decode($clear);
            // Strip out any url-encoded stuff
            if(!$skipURLDecode) {
                $clear = urldecode($clear);
            }
            // Replace non-AlNum characters with space
            if (!$excludeSpecialChars) {
                $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
            }
            // Replace Multiple spaces with single space
            $clear = preg_replace('/ +/', ' ', $clear);
            // Trim the string of leading/trailing space
            $clear = trim($clear);
        }         
        return $clear;
    }

    
       
}
