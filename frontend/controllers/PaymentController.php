<?php
namespace app\controllers;
namespace frontend\controllers;
use Yii;
use app\models\Users;
use app\models\Settings;
use app\models\ApiLog;
use app\models\Donations;
use app\models\Transactions;
use app\models\PaymentAttempts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;
include_once("common/components/xmlToArray.php");
include_once("common/components/XmlDomConstruct.php");

class PaymentController extends Controller
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
                case 'payment.create':
                    $this->makePayment($xmlArray['request']);
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
       public function makePayment($xmlpaymentDetails) {
      //check the mandatory fields
 
        if (!isset($xmlpaymentDetails['payment']['amount']) || trim($xmlpaymentDetails['payment']['amount']) == '') {
            
            $this->addLogEntry('payment.create', 'Failure', 9, 'payment amount missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'payment amount missing.'), 'error', 400);
            
        }else if (!isset($xmlpaymentDetails['payment']['id']) || trim($xmlpaymentDetails['payment']['id']) == '') {
            
            $this->addLogEntry('payment.create', 'Failure', 9, 'Payment ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Payment ID missing.'), 'error', 400);
            
        }else {
            $amount      = $this->sanitizeXML($xmlpaymentDetails['payment']['amount']);
            $id          = $this->sanitizeXML($xmlpaymentDetails['payment']['id']);
            
            
                
                
            //create a new payment
            $donations = Donations::findOne($id);
            if (isset($donations) && $donations != NULL) {
                $payment = PaymentAttempts::findOne($donations->payment_attempt_id);
               
                $handler = "frontend\\components\\" . $payment->handler;
                if ($payment->status == 'success') {
                    $class = new $handler;
                    $class->run($id);
                } else {
                    $this->addLogEntry('payment.create', 'Faliure', 3, 'Payment status is pending');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Payment status is pendind.'), 'error', 400);
                }
            } else {
                $this->addLogEntry('payment.create', 'Faliure', 3, 'Invalid donation Id');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid donation Id.'), 'error', 400);
            }
                //var_dump(class_exists($handler));
               
              
            
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
        $api_log->api_method                = $api_method;
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