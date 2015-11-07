<?php
namespace api\modules\v1\controllers;
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
include_once("../../common/components/xmlToArray.php");
include_once("../../common/components/XmlDomConstruct.php");
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
    public $close_time = "23:00:00";
    
    
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
                case 'close_time':
                    $this->close_time = $setting->value;
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
                $command = Yii::$app->db->createCommand("SELECT users.id FROM users WHERE id NOT IN (SELECT user_id FROM calendar_events  
                    WHERE (calendar_events.start >  '".date("Y-m-d H:i:s")."'
            AND  calendar_events.end <  '".date("Y-m-d")." ".$this->close_time."') )  AND users.role_id = 2 AND users.online = 1 ");
                $result1= $command->queryAll();
                //var_dump($result1);
                
                //if no doctor has empty calendar, look for one with the lest amount of schedule
                if(empty($result1[0])){
                $command = Yii::$app->db->createCommand("SELECT MIN(cnt) as cnt, user_id FROM (SELECT COUNT(*) cnt, user_id FROM calendar_events
                 WHERE (calendar_events.start >  '".date("Y-m-d")." 09:00:00'
            AND  calendar_events.end <  '".date("Y-m-d")." ".$this->close_time."') GROUP BY user_id) t;");
                $result= $command->queryAll();
                
                return $result[0]['user_id'];
                
                }else{
                return $result1[0]['id'];
                
                }
               //exit;
        
    }
    protected function getSlot($doc)
    {
        //get doctor's calendar for the day
            $calendar = CalendarEvents::find()->where("user_id = '$doc' and `start` >  '".date("Y-m-d H:i:s")."'
            AND  `end` <  '".date("Y-m-d")."  ".$this->close_time."'")->one();
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

    
    protected function generate_code($length) {
      $random = '';
      for ($i = 0; $i < $length; $i++) {
        $random .= rand(0, 1) ? rand(2, 9) : chr(rand(ord('a'), ord('z')));
      }
      return $random;
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
                
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId    = $xmlconsultationDetails['consultation']['user_id'];
            Yii::$app->AuthoriseUser->auth_key  = $xmlconsultationDetails['consultation']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){                
            
                //create a new Consultation
                $model = new CalendarEvents();
                $cons = new Consultations();
                $user = Patient::find()->where("user_id = '$user_id'")->one();
               
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
                $cons->details               = $note;
                $cons->user_id             = $user_id;
                $cons->code                 = $this->generate_code(6);
                $cons->save(false);
                //$activate_key               = time() . rand(1000, 9999);echo 1;
                $model->notes               = "Code: ".$cons->code."\n".$note;
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
                $doctor = Users::find()->where("id = '$doc'")->one();
                $this->addLogEntry('consultation.create', 'Success', 3, 'consultation successfully created. Username :- ' . $name, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Consultation successfully created.',"data"=>array(
                        "doctor"=>$doctor->title." ".$doctor->fname." ".$doctor->lname,
                        "start"=>$start,
                        "end"=>$end,
                        "code"=>$cons->code ,
                    )), 'ok', 200);               
                exit;
            
            } else {
                $this->addLogEntry('consultation.create', 'Failure', 9, 'Create consultation auth key authentication failed for user :' .$user_id);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
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