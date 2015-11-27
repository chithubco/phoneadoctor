<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\db\Query;
use common\models\Donations;
use app\models\Users;
use app\models\Settings;
use app\models\ApiLog;
use app\models\Transactions;
use common\models\PaymentAttempts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;
include_once("../../common/components/xmlToArray.php");
include_once("../../common/components/XmlDomConstruct.php");
class DonationController extends Controller
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
                case 'donation.create':
                    $this->createDonation($xmlArray['request']);
                    break;
                case 'donation.update':
                    $this->updateDonation($xmlArray['request']);
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
   
     /*
     * API Method : user.create
     * Purpose    : Create a user
     * Returns    : Result of insert operation
     */
       public function createDonation($xmldonationDetails) {
      //check the mandatory fields
 
        if (!isset($xmldonationDetails['donation']['amount']) || trim($xmldonationDetails['donation']['amount']) == '') {
            
            $this->addLogEntry('donation.create', 'Failure', 9, 'Donation amount missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Donation amount missing.'), 'error', 400);
            
        }else if (!isset($xmldonationDetails['donation']['gateway']) || trim($xmldonationDetails['donation']['gateway']) == '') {
            
            $this->addLogEntry('donation.create', 'Failure', 9, 'Payment gateway missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Payment gateway missing.'), 'error', 400);
            
        }else {
            $amount      = $this->sanitizeXML($xmldonationDetails['donation']['amount']);
            @$name       = $this->sanitizeXML($xmldonationDetails['donation']['name']);
            @$email      = $xmldonationDetails['donation']['email'];
            $gateway     = $this->sanitizeXML($xmldonationDetails['donation']['gateway']);
            
                
                
                //create a new donation
                $model = new Donations();
                $paym  = new PaymentAttempts();   

                $paym->amount               = $amount;                
                $paym->gateway              = $gateway;
                $paym->email                = $email;
                $paym->handler              = "Donation";
                $paym->status               = "pending"; 
                $paym->details              = "Donation from ".$name;
                $paym->save(false);
                
                $model->amount           = $amount;
                $model->name             = $name;
                $model->email            = $email;
                $model->payment_attempt_id = $paym->id;
                $model->status            = "pending"; 
                $model->save(false);
                
                $this->addLogEntry('donation.create', 'Success', 3, 'Donation successfully created. Username :- ' . $name, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Donation successfully created.',"response"=>array("payment_id"=>$model->id,"gateway"=>$gateway)), 'ok', 200);               
                exit;
            
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
       
        $date   = date("Y-m-d H:i:s");
        
        $api_log = new ApiLog;
        $api_log->api_method             = $api_method;
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
?>