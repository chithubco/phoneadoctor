<?php

namespace api\modules\v1\controllers;

use Yii;
use common\models\Users;
use app\models\Settings;
use app\models\ApiLog;
use common\models\Transactions;
use common\models\PaymentAttempts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;

include_once("../../common/components/xmlToArray.php");
include_once("../../common/components/XmlDomConstruct.php");

class DocumentController extends Controller
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
    public $folder = "../gaiaehr/sites/default/patients";

    
    
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
        //var_dump($_REQUEST);          
       
        //use php://input to get the raw $_POST results
        $xmlInput = file_get_contents('php://input');

        //parse the incoming XML to array
        $xmlArray = $_POST;
        //echo'<pre>';print_r($xmlInput);echo'</pre>';

        if (!isset($xmlArray['request']) || !isset($xmlArray['request_attr'])) {
            $this->generateJsonResponce(array("response_code" => 112, "description" => 'Invalid request, please check your input.'), 'error', 400);
        } else if ($requestMethod <> 'POST') {
            $this->generateJsonResponce(array("response_code" => 101, "description" => 'Only POST method supported.'), 'error', 403);
        } /*else if ($connectionType <> 443) {
            $this->generateJsonResponce(array("response_code" => 111, "description" => 'Insecure connection.'), 'error', 406);
        }*/ else {  
            //determine the method
            switch ($xmlArray['method']) {
                case 'document.create':
                    $this->upload($xmlArray,$_FILES);
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
       public function upload($xmldocumentDetails,$file) {

      //check the mandatory fields
 
        if (!isset($xmldocumentDetails['uid']) || trim($xmldocumentDetails['uid']) == '') {
            
            $this->addLogEntry('document.create', 'Failure', 9, 'document user id missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'document user id missing.'), 'error', 400);
            
        }else if (!isset($xmldocumentDetails['authkey']) || trim($xmldocumentDetails['authkey']) == '') {
            
            $this->addLogEntry('document.create', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);
            
        }else if (!isset($file['name']) || trim($file['name']) == '') {
            
            $this->addLogEntry('document.create', 'Failure', 9, 'File title missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'File title missing.'), 'error', 400);
            
        }else {
            $postdata = fopen( $_FILES[ 'data' ][ 'tmp_name' ], "r" );
        /* Get file extension */
        $title = $_FILES[ 'data' ][ 'name' ];
        $extension = substr( $_FILES[ 'data' ][ 'name' ], strrpos( $_FILES[ 'data' ][ 'name' ], '.' ) );
        if(!is_dir($this->folder."/".$xmldocumentDetails['uid']))
            mkdir($this->folder."/".$xmldocumentDetails['uid']);
        if(!is_dir($this->folder."/".$xmldocumentDetails['uid']."/uploaddoc"))
            mkdir($this->folder."/".$xmldocumentDetails['uid']."/uploaddoc");
        /* Generate unique name */
        $filename = $this->folder."/".$xmldocumentDetails['uid']."/uploaddoc/".$title. uniqid() . $extension;

        /* Open a file for writing */
        $fp = fopen( $filename, "w" );

        /* Read the data 1 KB at a time
          and write to the file */
        while( $data = fread( $postdata, 1024 ) )
            fwrite( $fp, $data );

        /* Close the streams */
        fclose( $fp );
        fclose( $postdata );

        /* the result object that is sent to client*/
        $model = new PatientDocuments;
                        $model->load($xmlUserDetails);
                        $model->title = $title;
                        $model->validate();
                        if($model->getErrors()){
                            $this->addLogEntry('user.medicals', 'Failure', 9, 'Correct the validation errors.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.','errors'=>$model->getErrors()), 'error', 400);
                        exit;
                        }
                        
                        $model->save();
        
               
                $this->addLogEntry('payment.create', 'Success', 3, 'payment successfully created. Username :- ' . $name, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'payment successfully created.'), 'ok', 200);               
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
